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
* Componente IPopUpObjeto

* Data de Criação: 03/10/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

Casos de uso: uc-03.04.05
              uc-03.05.15
              uc-03.05.14
*/

/*
$Log$
Revision 1.4  2007/03/21 14:22:03  bruce
Bug #8758#

Revision 1.3  2006/10/09 12:13:22  domluc
add setId
add Caso de Uso Convenios

Revision 1.2  2006/10/05 10:55:37  fernando
inclusão do uc-03.05.15

Revision 1.1  2006/10/04 08:53:13  cleisson
novo componente IPopUpObjeto

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';

/**
    * Classe que monta o HTML do IPopUpEditObjeto
    * @author Desenvolvedor: Diego Barbosa Victoria

*/

class IPopUpObjeto extends BuscaInner
{
    public function IPopUpObjeto(&$obForm)
    {
        parent::BuscaInner();
        $this->obForm = &$obForm;

        $this->setRotulo            ( 'Objeto' );
        $this->setTitle             ( 'Informe o objeto desejado.' );
        $this->obCampoCod->setName  ( 'stObjeto'  );
        $this->obCampoCod->setId    ( 'stObjeto'  );
        $this->obCampoCod->setAlign ( "left" );
        $this->setId                ( 'txtObjeto' );
        $this->setNull              ( true );
        $this->stTipoBusca          = 'popup';

    }
    public function setTipoBusca($stTipo) { $this->stTipoBusca = $stTipo; }

    public function montaHTML()
    {
        $this->setFuncaoBusca("abrePopUp('".CAM_GP_COM_POPUPS."objeto/FLProcurarObjeto.php','".$this->obForm->getName()."','".$this->obCampoCod->getName()."','".$this->getId()."','".$this->stTipoBusca."','".Sessao::getId()."','800','550');");

        $this->setValoresBusca( CAM_GP_COM_POPUPS."objeto/OCProcuraObjeto.php?".Sessao::getId(), $this->obForm->getName() );

        parent::montaHTML();
    }
}

?>
