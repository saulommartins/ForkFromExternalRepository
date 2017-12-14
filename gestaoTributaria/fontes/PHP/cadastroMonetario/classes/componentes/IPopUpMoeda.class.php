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
* Padronizar a busca por MOEDA, estendendo a classe BuscaInner
* Data de Criação: 19/12/2005

* @author Desenvolvedor: Diego Bueno Coelho

* @package URBEM
* @subpackage Componentes

    * $Id: IPopUpMoeda.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.06

*/

/*
$Log$
Revision 1.6  2006/10/20 17:40:15  dibueno
Campo ID

Revision 1.5  2006/10/19 11:23:27  dibueno
*** empty log message ***

Revision 1.4  2006/10/18 19:51:45  dibueno
Alterações na estrutura do código, passando o $obFormulario

Revision 1.3  2006/09/15 14:46:06  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once( CLA_BUSCAINNER );

class  IPopUpMoeda extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */

    /**
        * Metodo Construtor
        * @access Public
    */

    public function IPopUpMoeda()
    {
        parent::BuscaInner();

        $this->setRotulo                 ( 'Moeda'         );
        $this->setTitle                  ( 'Informe o código da Moeda.'  );
        $this->setId                     ( 'stNomMoeda'                  );
        $this->setNull                   ( false              );

        $this->obCampoCod->setName       ( "inCodMoeda"    );
        $this->obCampoCod->setSize       ( 10                  );
        $this->obCampoCod->setMaxLength  ( 10                 );
        $this->obCampoCod->setAlign      ( "left"             );
        //$this->obCampoCod->obEvento->setOnChange( "buscaValor('buscaMoeda');" );

        $this->stTipo = 'geral';
    }

    public function geraFormulario(&$obFormulario)
    {
        ;

        $pgOcul = "'".CAM_GT_MON_INSTANCIAS."moeda/OCManterMoeda.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."'";

        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaMoeda' );" );

        $this->setFuncaoBusca("abrePopUp('" . CAM_GT_MON_POPUPS . "moeda/FLProcurarMoeda.php','frm', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");

        $obFormulario->addComponente ( $this );
    }
}
?>
