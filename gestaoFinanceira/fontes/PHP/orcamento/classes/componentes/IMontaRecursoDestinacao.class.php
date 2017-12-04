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
    * Componente para Montagem do Recurso / Destinação de Recurso conforme configuração do Orçamento
    * Data de Criação   : 01/11/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: IMontaRecursoDestinacao.class.php 60928 2014-11-25 15:47:32Z arthur $

    * Casos de uso: uc-02.01.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );

class IMontaRecursoDestinacao extends Objeto
{
var $inCodRecurso;
var $stDescricaoRecurso;
var $boDestinacao;
var $obBscRecurso;
var $boArrecadado;
var $boFiltro;
var $boObrigatorioBarra;
var $stRotulo;
var $boNull;

function setCodRecurso($valor) { $this->inCodRecurso  = $valor; }
function setDescricaoRecurso($valor) { $this->stDescricaoRecurso  = $valor; }
function setArrecadado($valor) { $this->boArrecadado = $valor; }
function setFiltro($valor) { $this->boFiltro = $valor; }
function setLabel($valor) { $this->boArrecadado = $valor; $this->boFiltro = $valor;}
function setNull($valor) { $this->boNull = $valor; }
function setObrigatorioBarra($valor) { $this->boObrigatorioBarra = $valor; }
function setRotulo($valor) { $this->stRotulo = $valor; }
function setObrigatorioDestinacao($valor) { $this->boObrigatorioDestinacao = $valor; }

function getCodRecurso() { return $this->inCodRecurso; }
function getDescricaoRecurso() { return $this->stDescricaoRecurso; }
function getArrecadado() { return $this->boArrecadado; }
function getFiltro() { return $this->boFiltro; }
function getNull() { return $this->boNull; }
function getObrigatorioBarra() { return $this->boObrigatorioBarra; }
function getRotulo() { return $this->stRotulo; }
function getObrigatorioDestinacao() { return $this->boObrigatorioDestinacao;}

function IMontaRecursoDestinacao()
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );
    $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
    $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
    $obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
    $obTOrcamentoConfiguracao->consultar();
    // Recurso com Destinação de Recurso || 2008 em diante
    if ($obTOrcamentoConfiguracao->getDado("valor") == 'true') {
        $this->boDestinacao = true;
    }
    $this->setNull(true);
    $this->setObrigatorioDestinacao(true);
}

function geraFormulario(&$obFormulario)
{
        $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCRecurso.php?".Sessao::getId();
    $stLinkAjax = "ajaxJavaScript($pgOcul&stDestinacaoRecurso='+$('stDestinacaoRecurso').value+'&inCodDestinacao='+$('inCodDestinacao').value+'&inCodUso='+$('inCodUso').value+'&inCodDetalhamento='+$('inCodDetalhamento').value+'&inCodEspecificacao='+$('inCodEspecificacao').value,'preencheDestinacaoRecurso');";
    $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;

    if ($this->boDestinacao) { // Recurso com Destinações
        $obTOrcamentoConfiguracao->setDado("parametro","masc_recurso_destinacao");
        $obTOrcamentoConfiguracao->consultar();

        if ($this->getCodRecurso() != '' ) {
            include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecursoDestinacao.class.php"        );
            $obTOrcamentoRecursoDestinacao = new TOrcamentoRecursoDestinacao;
            $obTOrcamentoRecursoDestinacao->setDado('exercicio',Sessao::getExercicio() );
            $obTOrcamentoRecursoDestinacao->setDado('cod_recurso', $this->getCodRecurso() );
            $obTOrcamentoRecursoDestinacao->recuperaPorChave( $rsDestinacaoRecurso );

            $inCodUso = $rsDestinacaoRecurso->getCampo('cod_uso');
            $inCodDestinacao = $rsDestinacaoRecurso->getCampo('cod_destinacao');
            $inCodEspecificacao = $rsDestinacaoRecurso->getCampo('cod_especificacao');
            $inCodDetalhamento = $rsDestinacaoRecurso->getCampo('cod_detalhamento');

            $stDestinacaoRecurso = "$inCodUso.$inCodDestinacao.$inCodEspecificacao.$inCodDetalhamento";
            $arMascDestinacao = Mascara::validaMascaraDinamica( $obTOrcamentoConfiguracao->getDado('valor') , $stDestinacaoRecurso );
            $stDestinacaoRecurso = $arMascDestinacao[1];

        }
        $obHdnCodRecurso = new Hidden;
        $obHdnCodRecurso->setName ( 'inCodRecurso' );
        $obHdnCodRecurso->setId   ( 'inCodRecurso' );
        $obHdnCodRecurso->setValue( $this->inCodRecurso );

        $obTxtMascDestinacao = new TextBox;
        $obTxtMascDestinacao->setRotulo( "Destinação de Recurso" );
        $obTxtMascDestinacao->setTitle ( "Informe a Destinação de Recurso");
        $obTxtMascDestinacao->setName ( "stDestinacaoRecurso" );
        $obTxtMascDestinacao->setId   ( "stDestinacaoRecurso" );
        $obTxtMascDestinacao->setValue( $stDestinacaoRecurso );
        $obTxtMascDestinacao->setSize ( 12 );
        $obTxtMascDestinacao->setMaxLength ( strlen($obTOrcamentoConfiguracao->getDado('valor')));
        $obTxtMascDestinacao->obEvento->setOnKeyUp("mascaraDinamico('".$obTOrcamentoConfiguracao->getDado('valor')."', this, event);");
        $obTxtMascDestinacao->obEvento->setOnChange ( "ajaxJavaScript($pgOcul&stDestinacaoRecurso='+this.value,'preencheCombos');" );

        $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' ";

        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoIdentificadorUso.class.php"        );
        $obTOrcamentoIdentificadorUso = new TOrcamentoIdentificadorUso;
        $obTOrcamentoIdentificadorUso->recuperaTodos( $rsIDUSO, $stFiltro );

        $obCmbIDUSO = new Select;
        $obCmbIDUSO->setName      ( 'inCodUso'             );
        $obCmbIDUSO->setId        ( 'inCodUso'             );
        $obCmbIDUSO->setValue     ( $inCodUso              );
        $obCmbIDUSO->setStyle     ( "width:550px;" );
        $obCmbIDUSO->setRotulo    ( 'IDUSO'               );
        $obCmbIDUSO->setTitle     ( 'Selecione o Identificador de Uso.' );
        if(!$this->boFiltro) $obCmbIDUSO->setNull      ( false                 );
        $obCmbIDUSO->setCampoId   ( "cod_uso"           );
        $obCmbIDUSO->setCampoDesc ( "[cod_uso] - [descricao]" );
        $obCmbIDUSO->addOption    ( "", "Selecione"        );
        $obCmbIDUSO->obEvento->setOnChange( $stLinkAjax );
        $rsIDUSO->setLarguraOption("descricao",85);
        $obCmbIDUSO->preencheCombo( $rsIDUSO );

        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoDestinacaoRecurso.class.php"        );
        $obTOrcamentoDestinacaoRecurso = new TOrcamentoDestinacaoRecurso;
        $obTOrcamentoDestinacaoRecurso->recuperaTodos( $rsDestinacao, $stFiltro );

        $obCmbDestinacao = new Select;
        $obCmbDestinacao->setName      ( 'inCodDestinacao'             );
        $obCmbDestinacao->setId        ( 'inCodDestinacao'             );
        $obCmbDestinacao->setValue     ( $inCodDestinacao              );
        $obCmbDestinacao->setRotulo    ( 'Grupo de Destinação'               );
        $obCmbDestinacao->setStyle     ( "width:550px;" );
        $obCmbDestinacao->setTitle     ( 'Selecione o Grupo de Destinação de Recursos.' );
        if(!$this->boFiltro) $obCmbDestinacao->setNull      ( false                );
        $obCmbDestinacao->setCampoId   ( "cod_destinacao"           );
        $obCmbDestinacao->setCampoDesc ( "[cod_destinacao] - [descricao]" );
        $obCmbDestinacao->addOption    ( "", "Selecione"        );
        $obCmbDestinacao->obEvento->setOnChange( $stLinkAjax );
        $rsDestinacao->setLarguraOption("descricao",85);
        $obCmbDestinacao->preencheCombo( $rsDestinacao );

        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEspecificacaoDestinacaoRecurso.class.php"        );
        $obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;
        $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaTodos( $rsEspecDestinacao, $stFiltro );

        $obCmbEspecDestinacao = new Select;
        $obCmbEspecDestinacao->setName      ( 'inCodEspecificacao'             );
        $obCmbEspecDestinacao->setId        ( 'inCodEspecificacao'             );
        $obCmbEspecDestinacao->setValue     ( $inCodEspecificacao              );
        $obCmbEspecDestinacao->setRotulo    ( 'Especificação de Destinação'               );
        $obCmbEspecDestinacao->setStyle     ( "width:550px;" );
        $obCmbEspecDestinacao->setTitle     ( 'Selecione o Grupo de Destinação de Recursos.');
        if(!$this->boFiltro) $obCmbEspecDestinacao->setNull      ( false                 );
        $obCmbEspecDestinacao->setCampoId   ( "cod_especificacao"           );
        $obCmbEspecDestinacao->setCampoDesc ( "[cod_especificacao] - [descricao]" );
        $obCmbEspecDestinacao->addOption    ( "", "Selecione"        );
        $obCmbEspecDestinacao->obEvento->setOnChange( $stLinkAjax );
        $rsEspecDestinacao->setLarguraOption("descricao",85);

        $obCmbEspecDestinacao->preencheCombo( $rsEspecDestinacao );

        // Cria Hidden com o 'nome do recurso'
        if ($this->inCodRecurso) {
            $obTOrcamentoEspecificacaoDestinacaoRecurso->setDado("cod_especificacao", $inCodEspecificacao );
            $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaTodos( $rsEspecDestinacao, $stFiltro );
            $this->stDescricaoRecurso = $rsEspecDestinacao->getCampo('descricao');
        }
        $obHdnNomRecurso = new Hidden;
        $obHdnNomRecurso->setName          ( "stDescricaoRecurso" );
        $obHdnNomRecurso->setId            ( "stDescricaoRecurso" );
        $obHdnNomRecurso->setValue         ( $this->stDescricaoRecurso );

        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoDetalhamentoDestinacaoRecurso.class.php"        );
        $obTOrcamentoDetalhamentoDestinacaoRecurso = new TOrcamentoDetalhamentoDestinacaoRecurso;
        $obTOrcamentoDetalhamentoDestinacaoRecurso->recuperaTodos( $rsDetalhamentoDestinacao , $stFiltro);

        $obCmbDetalhamentoDestinacao = new Select;
        $obCmbDetalhamentoDestinacao->setName      ( 'inCodDetalhamento'             );
        $obCmbDetalhamentoDestinacao->setId        ( 'inCodDetalhamento'             );
        $obCmbDetalhamentoDestinacao->setValue     ( $inCodDetalhamento                 );
        $obCmbDetalhamentoDestinacao->setRotulo    ( 'Detalhamento da Destinação'               );
        $obCmbDetalhamentoDestinacao->setStyle     ( "width:550px;" );
        $obCmbDetalhamentoDestinacao->setTitle     ( 'Selecione o Grupo de Destinação de Recursos.');
        if(!$this->boFiltro) $obCmbDetalhamentoDestinacao->setNull      ( false                 );
        $obCmbDetalhamentoDestinacao->setCampoId   ( "cod_detalhamento"           );
        $obCmbDetalhamentoDestinacao->setCampoDesc ( "[cod_detalhamento] - [descricao]" );
        $obCmbDetalhamentoDestinacao->addOption    ( "", "Selecione"        );
        $obCmbDetalhamentoDestinacao->obEvento->setOnChange( $stLinkAjax );
        $rsDetalhamentoDestinacao->setLarguraOption("descricao",85);
        $obCmbDetalhamentoDestinacao->preencheCombo( $rsDetalhamentoDestinacao );

        if ($this->getArrecadado()) {
            $obTxtMascDestinacao->setLabel( true );
            $obCmbIDUSO->setLabel( true );
            $obCmbDestinacao->setLabel ( true );
            $obCmbEspecDestinacao->setLabel ( true );
            $obCmbDetalhamentoDestinacao->setLabel ( true );
        }

        if (!$this->getObrigatorioDestinacao()) {
            $obCmbIDUSO->setObrigatorio(false);
            $obCmbDestinacao->setObrigatorio(false);
            $obCmbEspecDestinacao->setObrigatorio(false);
            $obCmbDetalhamentoDestinacao->setObrigatorio(false);
        }

        $obFormulario->addHidden    ( $obHdnCodRecurso );
        $obFormulario->addHidden    ( $obHdnNomRecurso );
        $obFormulario->addComponente( $obTxtMascDestinacao );
        $obFormulario->addComponente( $obCmbIDUSO );
        $obFormulario->addComponente( $obCmbDestinacao );
        $obFormulario->addComponente( $obCmbEspecDestinacao );
        $obFormulario->addComponente( $obCmbDetalhamentoDestinacao );

    } else { // Recurso Antigo
        
        $obTOrcamentoConfiguracao->setDado("parametro","masc_recurso");
        $obTOrcamentoConfiguracao->consultar();
        if ($this->getArrecadado()) {
            $obLblRecurso = new Label;
            if ( $this->getRotulo() ) {
                $obLblRecurso->setRotulo( $this->getRotulo() );
            } else {
                $obLblRecurso->setRotulo( "Recurso"   );
            }
            
            $obLblRecurso->setId    ( "stRecurso" );
            $obLblRecurso->setValue ( $this->getDescricaoRecurso() == "" ? $this->getCodRecurso() : $this->getCodRecurso().' - '.$this->getDescricaoRecurso() );

            $obFormulario->addComponente( $obLblRecurso );
        } else {
            include_once ( CAM_GF_ORC_COMPONENTES."IPopUpRecurso.class.php" );
            $obIPopUpRecurso = new IPopUpRecurso;
            if ( $this->getRotulo() ) {
                $obIPopUpRecurso->setRotulo($this->getRotulo());
            }
            $obIPopUpRecurso->obCampoCod->setId( "inCodRecurso" );
            $obIPopUpRecurso->obImagem->setId  ( "imgRecurso" );
            $obIPopUpRecurso->setNull( $this->getNull() );
            if ( $this->getObrigatorioBarra() ) {
                $obIPopUpRecurso->setObrigatorioBarra(true);
            }

            if ($this->getCodRecurso() != NULL) {
                $obIPopUpRecurso->setCodRecurso($this->getCodRecurso());
            }

            if($this->getDescricaoRecurso())
                $obIPopUpRecurso->setDescricaoRecurso( $this->getDescricaoRecurso());

            $obFormulario->addComponente ( $obIPopUpRecurso );
        }

    }
}

}
