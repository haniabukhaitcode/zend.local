<?php
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        //
    }
    public function indexAction()
    {
        $this->view->title = "My Albums";
    }

    public function readAction()
    {
        $albums = new Application_Model_DbTable_Read();
        $albums = $albums->fetchAll();
        // return response as data key contains array of albums
        $this->_helper->json->sendJson(['data' => $albums->toArray()]);
    }



    public function editAction()
    {
        $form = new Application_Form_Read();
        $form->submit->setLabel('Save');
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $id = $form->getValue('id');
                $artist_id = $form->getValue('artist_id');
                $title = $form->getValue('title');
                $tags = $form->getValue('tags');
                $albums = new Application_Model_DbTable_Edit();
                $albums->editAlbum($id, $artist_id, $title, implode(',', $tags));
                $this->_helper->redirector('index');
            } else {
                $form->populate($formData);
            }
        } else {
            $id = $this->_getParam('id', 0);
            if ($id > 0) {
                $albums = new Application_Model_DbTable_Read();
                $form->populate($albums->getAlbum($id));
            }
        }
    }


    public function addAction()
    {
        $form = new Application_Form_Read();
        $form->submit->setLabel('Add')->setAttrib('action', 'add');
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $artist_id = $form->getValue('artist_id');
                $title = $form->getValue('title');
                $tag = $form->getValue('tags');
                $albums = new Application_Model_DbTable_Add();
                $albums->addAlbum($artist_id, $title, $tag);

                $this->_helper->redirector('index');
            } else {
                $form->populate($formData);
            }
        }
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isPost()) {
            $delete = $this->getRequest()->getPost('delete');
            if ($delete == 'Yes') {
                $id = $this->getRequest()->getPost('id');
                $albums = new Application_Model_DbTable_Delete();
                $albums->deleteAlbum($id);
            }
            $this->_helper->redirector('index');
        } else {
            $id = $this->_getParam('id', 0);
            $albums = new Application_Model_DbTable_Read();
            $this->view->album = $albums->getAlbum($id);
        }
    }
}
