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
    * Arquivo de instância para manutenção dos processos
    * Data de Criação: 11/10/2006

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    Casos de uso: uc-01.06.98

    $Id: FMManterProcesso.php 62581 2015-05-21 14:05:03Z michel $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';

include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloProcesso.class.php";
include_once CAM_GA_PROT_COMPONENTES."ISelectClassificacaoAssunto.class.php";
include_once CAM_GA_PROT_COMPONENTES."IChkDocumentoProcesso.class.php";
//include_once CAM_FRAMEWORK."legado/funcoesLegado.lib.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloAndamento.class.php";
include_once CAM_GA_PROT_MAPEAMENTO."TPROAtributoProtocolo.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";

Sessao::write('codigo_processo',$_REQUEST['inCodigoProcesso']);

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcesso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos');;
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : 'alterar';

$obTProtocoloAndamento = new TProtocoloAndamento;
$obTProtocoloAndamento->recuperaTodos($rsRecordAndamento, " WHERE cod_processo = ".$_REQUEST['inCodigoProcesso']."
                                                              AND ano_exercicio = '".$_REQUEST['inAnoExercicio']."'",
                                                          " ORDER BY cod_andamento DESC LIMIT 1");

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->recuperaTodos($rsCentroCusto, " WHERE exercicio <= '".Sessao::getExercicio()."'
                                                                 AND cod_modulo = 5
                                                                 AND parametro = 'centro_custo'
                                                            ORDER BY exercicio DESC LIMIT 1");

$boCentroCusto = false;
$rsListaCentroCusto = new RecordSet();
while (!$rsCentroCusto->eof()) {
    $boCentroCusto = $rsCentroCusto->getCampo("valor");

    $rsCentroCusto->proximo();
}

$codOrgao = $rsRecordAndamento->getCampo('cod_orgao');
$codAndamento = $rsRecordAndamento->getCampo('cod_andamento');

$obIMontaOrganograma = new IMontaOrganograma;
$obIMontaOrganograma->setNivelObrigatorio(1);
$obIMontaOrganograma->setCodOrgao($codOrgao);

$obTProcesso = new TProcesso;
$obTProcesso->setDado('cod_processo',$_REQUEST['inCodigoProcesso']);
$obTProcesso->setDado('ano_exercicio',$_REQUEST['inAnoExercicio']);
$obTProcesso->recuperaPorChave($rsRecordProcesso);

$codSituacao = $rsRecordProcesso->getCampo('cod_situacao');

$obHdnCtrl = new hidden;
$obHdnCtrl->setName( 'stCtrl' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obTProtocoloProcesso = new TProtocoloProcesso();
$stProcesso = $obTProtocoloProcesso->mascararProcesso($_REQUEST['inCodigoProcesso'], $_REQUEST['inAnoExercicio']);

Sessao::write('filtro',array('inCodigoProcesso' => $_REQUEST['inCodigoProcesso'], 'inAnoExercicio' => $_REQUEST['inAnoExercicio']));

$obTProtocoloProcesso->setDado('cod_processo', $_REQUEST['inCodigoProcesso']);
$obTProtocoloProcesso->setDado('ano_exercicio',$_REQUEST['inAnoExercicio']);
$obTProtocoloProcesso->recuperaPorChave($rsProcesso);

if($boCentroCusto=='true'){
    include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoCentroCusto.class.php';
    $obTAlmoxaridadoCentroCusto = new TAlmoxarifadoCentroCusto;
    $obTAlmoxaridadoCentroCusto->recuperaTodos($rsListaCentroCusto, '', 'descricao');
}

$obTPROAtributoProtocolo = new TPROAtributoProtocolo;
$obTPROAtributoProtocolo->setDado('cod_classificacao',$_REQUEST['inCodigoClassificacao']);
$obTPROAtributoProtocolo->setDado('cod_assunto',$_REQUEST['inCodigoAssunto']);
$obTPROAtributoProtocolo->recuperaAtributoAssunto($rsRecordProtocolo);

while (!$rsRecordProtocolo->eof()) {
    $codAtributo = $rsRecordProtocolo->getCampo("cod_atributo");
    $tipo        = $rsRecordProtocolo->getCampo("tipo");
    $valorLista  = $rsRecordProtocolo->getCampo("valor_padrao");

    if ($tipo == "l") {
        $lista = explode("\n", $valorLista);
    }

    $rsRecordProtocolo->proximo();
}

$obTPROAtributoProtocolo->setDado('cod_classificacao',$_REQUEST['inCodigoClassificacao']);
$obTPROAtributoProtocolo->setDado('cod_assunto',$_REQUEST['inCodigoAssunto']);
$obTPROAtributoProtocolo->setDado('cod_processo',$_REQUEST['inCodigoProcesso']);
$obTPROAtributoProtocolo->setDado('ano_exercicio',$_REQUEST['inAnoExercicio']);
$obTPROAtributoProtocolo->recuperaAtributoValor($rsRecordProtocolo);

if ($rsRecordProtocolo->getNumLinhas() > 0) {
    while (!$rsRecordProtocolo->eof()) {
        $nomAtributo = $rsRecordProtocolo->getCampo("nom_atributo");
        $tipo        = $rsRecordProtocolo->getCampo("tipo");

        if ($tipo == "l") {
            $numValor = $rsRecordProtocolo->getCampo("valor");
            $listaTipoCmb = explode("\n", $tipo);
        }

        if ($tipo == "t") {
            $stTexto = $rsRecordProtocolo->getCampo("valor");
            $listaTipoTxt = explode("\n", $tipo);
        }

        if ($tipo == "n") {
            $numNumero = $rsRecordProtocolo->getCampo("valor");
            $listaTipoNum = explode("\n", $tipo);
        }

        $rsRecordProtocolo->proximo();
    }
}

if ($lista == "") {
    $lista = array();
}

$rsLista = new RecordSet();
$rsLista->preenche($lista);

$obHdnProcesso = new hidden();
$obHdnProcesso->setName( 'stChaveProcesso' );
$obHdnProcesso->setValue( $_REQUEST['inCodigoProcesso'].'/'.$_REQUEST['inAnoExercicio'] );

$obLblProcesso = new Label();
$obLblProcesso->setRotulo("Processo");
$obLblProcesso->setValue($stProcesso);

$obTxtObservacoes = new TextArea();
$obTxtObservacoes->setRotulo('Observações');
$obTxtObservacoes->setNull(false);
$obTxtObservacoes->setName('stObservacoes');
$obTxtObservacoes->setCols(40);
$obTxtObservacoes->setRows(4);
$obTxtObservacoes->setValue($rsProcesso->getCampo('observacoes') );

$obTxtResumo = new TextBox();
$obTxtResumo->setName('stResumo');
$obTxtResumo->setRotulo('Assunto Resumido');
$obTxtResumo->setSize(80);
$obTxtResumo->setMaxLength(80);
$obTxtResumo->setValue( $rsProcesso->getCampo('resumo_assunto') );
$obTxtResumo->obEvento->setOnBlur ("montaParametrosGET('aaaa');"); 

$obCmbCentroCusto = new Select();
$obCmbCentroCusto->setName("centroCusto");
$obCmbCentroCusto->setRotulo('Centro de Custo');
$obCmbCentroCusto->setValue($rsProcesso->getCampo("cod_centro"));
$obCmbCentroCusto->setNull(false);
$obCmbCentroCusto->setCampoID( "[cod_centro]" );
$obCmbCentroCusto->setCampoDesc( "descricao" );
$obCmbCentroCusto->setStyle( "width: 200px" );
$obCmbCentroCusto->addOption( '', 'Selecione' );
$obCmbCentroCusto->preencheCombo( $rsListaCentroCusto );

$obISelectClassificacaoAssunto = new ISelectClassificacaoAssunto;
$obISelectClassificacaoAssunto->setNull                     ( false               );
$obISelectClassificacaoAssunto->obTxtChave->setName         ( 'codClassifAssunto' );
$obISelectClassificacaoAssunto->obCmbClassificacao->setName ( 'codClassificacao'  );
$obISelectClassificacaoAssunto->obCmbAssunto->setName       ( 'codAssunto'        );
$obISelectClassificacaoAssunto->obCmbClassificacao->setValue($rsProcesso->getCampo('cod_classificacao'));
$obISelectClassificacaoAssunto->obCmbAssunto->setValue($rsProcesso->getCampo('cod_assunto'));
$stCaminho = CAM_PROTOCOLO."protocolo/processos/".$pgOcul;
$stParametros = "'documento&codClassifAssunto=' + document.frm.codClassifAssunto.value";
$obISelectClassificacaoAssunto->obTxtChave->obEvento->setOnChange("ajaxJavaScript('".$stCaminho."',".$stParametros.");");
$obISelectClassificacaoAssunto->obCmbClassificacao->obEvento->setOnChange("document.getElementById('obCmpDocumento').style.display = 'none';");
$obISelectClassificacaoAssunto->obCmbAssunto->obEvento->setOnChange("document.getElementById('obCmpDocumento').style.display = 'none';");

$obIChkDocumentoProcesso = new IChkDocumentoProcesso();
$obIChkDocumentoProcesso->setCodigoClassificacao($_GET['inCodigoClassificacao']);
$obIChkDocumentoProcesso->setCodigoAssunto($_GET['inCodigoAssunto']);
$obIChkDocumentoProcesso->setCodProcesso($_REQUEST['inCodigoProcesso']);
$obIChkDocumentoProcesso->setAnoProcesso($_REQUEST['inAnoExercicio']);

$obFormulario = new Formulario();
$obIChkDocumentoProcesso->geraFormulario($obFormulario);
$obFormulario->montaInnerHTML();

$obSpnDocumentos = new Span();
$obSpnDocumentos->setId('obCmpDocumento');
$obSpnDocumentos->setValue( $obFormulario->getHTML() );
unset( $obFormulario );

include_once CAM_GA_ADM_COMPONENTES.'IMontaAssinaturas.class.php';
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setOpcaoAssinaturas( false );
$obMontaAssinaturas->obRadioAssinaturasSim->obEvento->setOnClick("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stIncluirAssinaturas='+this.value, 'montaEntidade');");
$obMontaAssinaturas->obRadioAssinaturasNao->obEvento->setOnClick("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stIncluirAssinaturas='+this.value, 'montaEntidade');");

$obSpnEntidade = new Span();
$obSpnEntidade->setId('spnEntidade');

$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados do processo');
$obFormulario->addHidden( $obHdnCtrl);
$obFormulario->addHidden($obHdnProcesso);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addComponente($obLblProcesso);
$obFormulario->addComponente($obTxtObservacoes);
$obFormulario->addComponente($obTxtResumo);
$obISelectClassificacaoAssunto->geraFormulario($obFormulario);

if($boCentroCusto=='true')
    $obFormulario->addComponente($obCmbCentroCusto);

if ($codSituacao == '2' || ($codSituacao == '3' && $codAndamento == 0)) {
    $obIMontaOrganograma->geraFormulario($obFormulario);
}

$obFormulario->addSpan($obSpnDocumentos);

if ($rsRecordProtocolo->getNumLinhas() > 0) {
    $obFormulario->addTitulo('Atributos de Assunto de Processo');

    if ($listaTipoTxt[0] == "t") {
        while (!$rsRecordProtocolo->eof()){
            $codAtributo = $rsRecordProtocolo->getCampo("cod_atributo");
            $nomAtributo = $rsRecordProtocolo->getCampo("nom_atributo");
            $tipo        = $rsRecordProtocolo->getCampo("tipo");

            if ($tipo == "t") {
                $stTexto = $rsRecordProtocolo->getCampo("valor");

                $obTxtAtributosProcessos = new TextBox();
                $obTxtAtributosProcessos->setName("valorAtributo[".$codAtributo."]");
                $obTxtAtributosProcessos->setSize('60');
                $obTxtAtributosProcessos->setMaxLength('50');
                $obTxtAtributosProcessos->setRotulo($nomAtributo);
                $obTxtAtributosProcessos->setValue($stTexto);

                $obFormulario->addComponente($obTxtAtributosProcessos);
            }
            $rsRecordProtocolo->proximo();
        }
    }

    if ($listaTipoNum[0] == "n"){
        while (!$rsRecordProtocolo->eof()){
            $codAtributo = $rsRecordProtocolo->getCampo("cod_atributo");
            $nomAtributo = $rsRecordProtocolo->getCampo("nom_atributo");
            $tipo        = $rsRecordProtocolo->getCampo("tipo");

            if ($tipo == "t") {
                $stTexto = $rsRecordProtocolo->getCampo("valor");

                $obTxtAtributosProcessos = new TextBox();
                $obTxtAtributosProcessos->setName("valorAtributo[".$codAtributo."]");
                $obTxtAtributosProcessos->setSize('60');
                $obTxtAtributosProcessos->setMaxLength('50');
                $obTxtAtributosProcessos->setRotulo($nomAtributo);
                $obTxtAtributosProcessos->setValue($stTexto);

                $obFormulario->addComponente($obTxtAtributosProcessos);
            }
            $rsRecordProtocolo->proximo();
        }
    }

    if ($listaTipoCmb[0] == "l") {
        while (!$rsRecordProtocolo->eof()){
            $codAtributo = $rsRecordProtocolo->getCampo("cod_atributo");
            $nomAtributo = $rsRecordProtocolo->getCampo("nom_atributo");
            $tipo        = $rsRecordProtocolo->getCampo("tipo");

            if ($tipo == "l") {
                $numValor = $rsRecordProtocolo->getCampo("valor");

                $obCmbAtributosProcesso = new Select();
                $obCmbAtributosProcesso->setName("valorAtributo[".$codAtributo."]");
                $obCmbAtributosProcesso->setRotulo($nomAtributo);
                $obCmbAtributosProcesso->setValue($numValor);
                $obCmbAtributosProcesso->setStyle ( "width: 200px" );
                $obCmbAtributosProcesso->addOption ( '', 'Selecione' );

                while (list($key, $val) = each($lista)) {
                    $val = trim($val);
                    $obCmbAtributosProcesso->addOption($val, $val);
                }

                $obFormulario->addComponente($obCmbAtributosProcesso);
            }
            
            $rsRecordProtocolo->proximo();
        }
    }
}

$obFormulario->addSpan($obSpnEntidade);
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->Cancelar($pgList);
$obFormulario->show();
?>
