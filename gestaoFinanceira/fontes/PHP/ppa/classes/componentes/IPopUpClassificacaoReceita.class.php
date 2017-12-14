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
    * Página Componente de classificação receita

    * Data de Criação   :30/10/2008

    * @author Analista      : Bruno Ferreira
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id:

    *Casos de uso: uc-02.09.02
*/

include_once(CLA_BUSCAINNER);

class  IPopUpClassificacaoReceita extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;

    /**
        * Metodo Construtor
        * @access Public
    */
    public function IPopUpClassificacaoReceita($obForm)
    {
        parent::BuscaInner();
        $this->setRotulo               ( "Classificação de Receita" );
        $this->setTitle                ( "Informe a rubrica de receita." );
        $this->setNulL                 ( false );
        $this->setId                   ( "stDescricaoReceita" );
        $this->setValue                ( $_REQUEST['stDescricao'] );
        $this->obCampoCod->setName     ( "inCodReceita" );
        $this->obCampoCod->setValue    ( $_GET['stMascClassReceita'] );
        $this->obCampoCod->setAlign    ("left");
        $this->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
        $this->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
        $this->obCampoCod->obEvento->setOnBlur ("buscaValor('mascaraClassificacao',".CAM_GF_ORC_POPUPS."'classificacaoreceita/OCClassificacaoReceita.php?','".$pgProc."','oculto','".Sessao::getId()."');");
        $this->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaoreceita/FLClassificacaoReceita.php','frm','inCodReceita','stDescricaoReceita','','".Sessao::getId()."','800','550');" );
        $this->setValoresBusca( CAM_GF_ORC_POPUPS.'classificacaoreceita/OCClassificacaoReceita.php?'.Sessao::getId(), $obForm->getName(), '' );

    }

}
?>
