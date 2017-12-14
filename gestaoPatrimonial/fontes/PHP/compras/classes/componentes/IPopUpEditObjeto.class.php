<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Componente IPopUpEditObjeto

* Data de Criação: 22/06/2006

* @author Desenvolvedor: Tonismar Régis Bernardo

Casos de uso: uc-03.04.01
              uc-03.04.07

*/

/*
$Log$
Revision 1.7  2007/05/31 14:26:07  hboaventura
Bug #9179#

Revision 1.6  2006/11/29 10:59:31  rodrigo
7558

Revision 1.5  2006/09/25 09:38:12  rodrigo
*** empty log message ***

Revision 1.4  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once( CLA_POPUPEDIT );

/**
    * Classe que monta o HTML do IPopUpEditObjeto
    * @author Desenvolvedor: Tonismar Régis Bernardo

*/

class IPopUpEditObjeto extends PopUpEdit
{
    public function IPopUpEditObjeto(&$obForm)
    {
        parent::PopUpEdit($obForm);
        $this->obForm = &$obForm;

        $this->setRotulo            ( 'Objeto' );
        $this->setTitle             ( 'Informe o objeto desejado.' );
        $this->obCampoCod->setName  ( 'stObjeto'  );
        $this->obCampoCod->setNull  ( false  );
        $this->obCampoTexto->setName( 'txtObjeto' );
        $this->obCampoTexto->setId  ( 'txtObjeto' );
    }

    public function montaHTML()
    {
        $this->setFuncaoBusca("abrePopUp('".CAM_GP_COM_POPUPS."objeto/FLProcurarObjeto.php','".$this->obForm->getName()."','".$this->obCampoCod->getName()."','".$this->obCampoTexto->getName()."','','".Sessao::getId()."','800','550');");

        $this->setValoresBusca( CAM_GP_COM_POPUPS."objeto/OCProcuraObjeto.php?".Sessao::getId(), $this->obForm->getName() );

        parent::montaHTML();
    }
}

?>
