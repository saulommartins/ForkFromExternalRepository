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
    * Página de Frame Oculto para Consulta de Edifcações do Imóvel
    * Data de Criação   : 14/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    * $Id: OCConsultaImovelConstrucao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

$stCtrl = $request->get("stCtrl");

switch ($stCtrl) {
    case "edificacao":
        $obRCIMConfiguracao = new RCIMConfiguracao;
        $obRCIMConfiguracao->setCodigoModulo( 12 );
        $obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
        $obRCIMConfiguracao->consultarConfiguracao();
        $obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

        $obRCIMEdificacao = new RCIMEdificacao;
        $obRCIMEdificacao->boListarBaixadas = true;

        if ($_REQUEST["inscricao_municipal"] AND $_REQUEST["cod_construcao"]) {

            $obRCIMEdificacao->obRCIMImovel->setNumeroInscricao( $_REQUEST["inscricao_municipal"] );
            $obRCIMEdificacao->setCodigoConstrucao      ( $_REQUEST["cod_construcao"] );
            $obRCIMEdificacao->listarEdificacoesConsulta( $rsEdificacao               );
            $flAreaEdificacao = $rsEdificacao->getCampo ( 'area' );
        } else {
            $obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["cod_construcao"] );
            $obRCIMEdificacao->setTipoVinculo   ( 'Condomínio'        );
            $obRCIMEdificacao->listarEdificacoes( $rsEdificacao );
            $obRCIMEdificacao->buscaAreaConstrucaoCondominio( $flAreaEdificacao );
        }

        $obRCIMEdificacao->listarProcessos( $rsListaProcesso );
        $rsListaProcesso->addFormatacao( 'area' , 'NUMERIC_BR');

        $arChaveAtributo = array( "cod_tipo" => $rsEdificacao->getCampo('cod_tipo'), "cod_construcao" => $_REQUEST["cod_construcao"] );
        $obRCIMEdificacao->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributo       );
        $obRCIMEdificacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosEdificacao );

        $obForm = new Form;
        $obForm->setAction( $pgProc  );
        $obForm->setTarget( "oculto" );

        $obHdnTipoVinculo = new Hidden;
        $obHdnTipoVinculo->setName ( "stTipoVinculo" );
        $obHdnTipoVinculo->setValue( $_REQUEST['tipo_vinculo'] );

        $obLblCodigo = new Label;
        $obLblCodigo->setRotulo      ( "Código" );
        $obLblCodigo->setValue       ( $rsEdificacao->getCampo('cod_construcao') );

        $obLblEdificacao = new Label;
        $obLblEdificacao->setRotulo  ( "Tipo de Edificação" );
        $obLblEdificacao->setValue   ( $rsEdificacao->getCampo('nom_tipo') );

        $obLblUnidade = new Label;
        $obLblUnidade->setRotulo     ( "Tipo de Unidade" );
        $obLblUnidade->setValue      ( $rsEdificacao->getCampo('tipo_vinculo') );

        $obLblDtConstrucao = new Label;
        $obLblDtConstrucao->setRotulo( "Data de Edificação" );
        $obLblDtConstrucao->setValue ( $rsEdificacao->getCampo('data_construcao') );

        $rsEdificacao->addFormatacao( 'area_unidade' , 'NUMERIC_BR');
        $obLblAreaUnidade = new Label;
        $obLblAreaUnidade->setRotulo ( "Área da Edificação" );
        $obLblAreaUnidade->setValue  ( $flAreaEdificacao );

        if ( $rsEdificacao->getCampo('cod_processo') ) {
            $stProcesso = $rsEdificacao->getCampo('cod_processo')."/".$rsEdificacao->getCampo('exercicio');
            $arProcesso = SistemaLegado::validaMascaraDinamica( $stMascaraProcesso ,$stProcesso );
        }
        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo    ( "Processo"     );
        $obLblProcesso->setValue     ( $arProcesso[1] );

        if ( $rsEdificacao->getCampo('data_baixa') && ($rsEdificacao->getCampo('data_termino') == "") ) {
            $stSituacao = "Baixado";

            $obLblMotivo = new Label;
            $obLblMotivo->setRotulo( "Justificativa" );
            $obLblMotivo->setValue ( $rsEdificacao->getCampo('justificativa') );

            $obLblDataBaixa = new Label;
            $obLblDataBaixa->setRotulo( "Data da Baixa" );
            $obLblDataBaixa->setValue ( $rsEdificacao->getCampo('data_baixa') );
        } else {
            $stSituacao = "Ativo";
        }
        $obLblSituacao = new Label;
        $obLblSituacao->setRotulo    ( "Situação" );
        $obLblSituacao->setValue     ( $stSituacao );

        $obMontaAtributosEdificacao = new MontaAtributos;
        $obMontaAtributosEdificacao->setTitulo     ( "Atributos da edificação" );
        $obMontaAtributosEdificacao->setName       ( "AtributoEdificacao_"     );
        $obMontaAtributosEdificacao->setLabel      ( true                      );
        $obMontaAtributosEdificacao->setRecordSet  ( $rsAtributosEdificacao    );

        $obFormulario = new Formulario;
        $obFormulario->addForm      ( $obForm            );
        $obFormulario->addTitulo    ( "Dados da edificacação" );
        $obFormulario->addHidden    ( $obHdnTipoVinculo  );
        $obFormulario->addComponente( $obLblCodigo       );
        $obFormulario->addComponente( $obLblEdificacao   );
        $obFormulario->addComponente( $obLblUnidade      );
        $obFormulario->addComponente( $obLblDtConstrucao );
        $obFormulario->addComponente( $obLblAreaUnidade  );
        $obFormulario->addComponente( $obLblProcesso     );
        $obFormulario->addComponente( $obLblSituacao     );
        if ($stSituacao == 'Baixado') {
            $obFormulario->addComponente( $obLblDataBaixa );
            $obFormulario->addComponente( $obLblMotivo    );
        }
        $obMontaAtributosEdificacao->geraFormulario ( $obFormulario );

        include_once 'FMConsultaImovelEdificacaoListaProcessos.php';
        $obFormulario->addSpan      ( $obSpnProcessoEdificacao          );
        $obFormulario->addSpan      ( $obSpnAtributosProcessoEdificacao );

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $stJs = "d.getElementById('spnEdificacao').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "visualizarProcessoEdificacao":
        $obRCIMEdificacao = new RCIMEdificacao;
        $obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["cod_construcao"] );

        if ($_REQUEST['stTipoVinculo'] == "Condomínio") {
            $obRCIMEdificacao->setTipoVinculo   ( 'Condomínio'  );
            $obRCIMEdificacao->boListarBaixadas = false;
            $obRCIMEdificacao->listarEdificacoes( $rsEdificacaoAtiva );
            $obRCIMEdificacao->boListarBaixadas = true;
            $obRCIMEdificacao->listarEdificacoes( $rsEdificacaoBaixada );

            $obRCIMEdificacao->setTimestampConstrucao( $_REQUEST['timestamp'] );
            $obRCIMEdificacao->buscaAreaConstrucaoCondominio( $flAreaEdificacao );
        } else {
            $obRCIMEdificacao->boListarBaixadas = false;
            $obRCIMEdificacao->listarEdificacoesImovel( $rsEdificacaoAtiva );
            $obRCIMEdificacao->boListarBaixadas = true;
            $obRCIMEdificacao->listarEdificacoesImovel( $rsEdificacaoBaixada );

            $flAreaEdificacao = $_REQUEST["area"];
        }

        $rsEdificacao = new RecordSet;
        if ($rsEdificacaoBaixada->getNumLinhas() > 0 && $rsEdificacaoAtiva->getNumLinhas() > 0) {
            $rsEdificacao->preenche(array_merge($rsEdificacaoAtiva->getElementos(), $rsEdificacaoBaixada->getElementos()));
        }else
            if ($rsEdificacaoBaixada->getNumLinhas() > 0) {
                $rsEdificacao->preenche( $rsEdificacaoBaixada->getElementos() );
            }else
                if ( $rsEdificacaoAtiva->getNumLinhas() > 0 ) {
                    $rsEdificacao->preenche( $rsEdificacaoAtiva->getElementos() );
                }

        $arChaveAtributo = array( "cod_tipo" => $rsEdificacao->getCampo('cod_tipo'), "cod_construcao" => $_REQUEST["cod_construcao"]
                                  ,"timestamp" => $_REQUEST[timestamp], "cod_processo" => $_REQUEST["cod_processo"] );
        $obRCIMEdificacao->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributo       );
        $obRCIMEdificacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosEdificacao );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo( "Processo" );
        $obLblProcesso->setValue ( $_REQUEST["cod_processo_ano"] );

        $obLblArea = new Label;
        $obLblArea->setRotulo    ( "Área da Construção" );
        $obLblArea->setValue     ( $flAreaEdificacao    );

        $obMontaAtributosEdificacao = new MontaAtributos;
        $obMontaAtributosEdificacao->setTitulo     ( "Atributos da edificação" );
        $obMontaAtributosEdificacao->setName       ( "AtributoEdificacao_"     );
        $obMontaAtributosEdificacao->setLabel      ( true                      );
        $obMontaAtributosEdificacao->setRecordSet  ( $rsAtributosEdificacao    );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso  );
        $obFormularioProcesso->addComponente( $obLblArea  );
        $obMontaAtributosEdificacao->geraFormulario( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcessoEdificacao').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "construcao":
        $obRCIMConfiguracao = new RCIMConfiguracao;
        $obRCIMConfiguracao->setCodigoModulo( 12 );
        $obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
        $obRCIMConfiguracao->consultarConfiguracao();
        $obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

        $obRCIMConstrucaoOutros = new RCIMConstrucaoOutros;
        $obRCIMConstrucaoOutros->setCodigoConstrucao( $_REQUEST["cod_construcao"] );
        $obRCIMConstrucaoOutros->listarConstrucoes( $rsConstrucao    );

        $obRCIMConstrucaoOutros->listarProcessos  ( $rsListaProcesso );
        $rsListaProcesso->addFormatacao( 'area' , 'NUMERIC_BR');

        $arChaveAtributo = array( "cod_construcao" => $_REQUEST["cod_construcao"] );
        $obRCIMConstrucaoOutros->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributo       );
        $obRCIMConstrucaoOutros->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosConstrucao );

        $obForm = new Form;
        $obForm->setAction( $pgProc  );
        $obForm->setTarget( "oculto" );

        $obHdnTipoVinculo = new Hidden;
        $obHdnTipoVinculo->setName ( "stTipoVinculo" );
        $obHdnTipoVinculo->setValue( $_REQUEST['tipo_vinculo'] );

        $obLblCodigo = new Label;
        $obLblCodigo->setRotulo      ( "Código" );
        $obLblCodigo->setValue       ( $rsConstrucao->getCampo('cod_construcao') );

        $obLblDtConstrucao = new Label;
        $obLblDtConstrucao->setRotulo( "Data de Construção" );
        $obLblDtConstrucao->setValue ( $rsConstrucao->getCampo('data_construcao') );

        $obLblDescricao = new Label;
        $obLblDescricao->setRotulo  ( "Descrição" );
        $obLblDescricao->setValue   ( $rsConstrucao->getCampo('descricao') );

        $rsConstrucao->addFormatacao( 'area_real' , 'NUMERIC_BR');
        $obLblAreaUnidade = new Label;
        $obLblAreaUnidade->setRotulo ( "Área da Construção" );
        $obLblAreaUnidade->setValue  ( $rsConstrucao->getCampo('area_real') );

        $obLblSituacao = new Label;
        $obLblSituacao->setRotulo    ( "Situação" );
        $obLblSituacao->setValue     ( $rsConstrucao->getCampo( 'situacao' ) );

        $obLblDataBaixa = new Label;
        $obLblDataBaixa->setRotulo   ( "Data da baixa" );
        $obLblDataBaixa->setValue    ( $rsConstrucao->getCampo( 'data_baixa' ) );

        $obLblJustificativa = new Label;
        $obLblJustificativa->setRotulo ( "Justificativa" );
        $obLblJustificativa->setValue  ( $rsConstrucao->getCampo( 'justificativa' ) );

        if ( $rsConstrucao->getCampo('cod_processo') ) {
            $stProcesso = $rsConstrucao->getCampo('cod_processo')."/".$rsConstrucao->getCampo('exercicio');
            $arProcesso = SistemaLegado::validaMascaraDinamica( $stMascaraProcesso ,$stProcesso );
        }
        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo    ( "Processo"     );
        $obLblProcesso->setValue     ( $arProcesso[1] );

        $obMontaAtributosConstrucao = new MontaAtributos;
        $obMontaAtributosConstrucao->setTitulo     ( "Atributos da construção" );
        $obMontaAtributosConstrucao->setName       ( "AtributoConstrucao_"     );
        $obMontaAtributosConstrucao->setLabel      ( true                      );
        $obMontaAtributosConstrucao->setRecordSet  ( $rsAtributosConstrucao    );

        $obFormulario = new Formulario;
        $obFormulario->addForm      ( $obForm            );
        $obFormulario->addTitulo    ( "Dados da construção" );
        $obFormulario->addHidden    ( $obHdnTipoVinculo  );
        $obFormulario->addComponente( $obLblCodigo       );
        $obFormulario->addComponente( $obLblDtConstrucao );
        $obFormulario->addComponente( $obLblDescricao    );
        $obFormulario->addComponente( $obLblAreaUnidade  );
        $obFormulario->addComponente( $obLblProcesso     );
        $obFormulario->addComponente( $obLblSituacao     );
       if ( $rsConstrucao->getCampo( 'data_baixa' ) != "" ) {
            $obFormulario->addComponente( $obLblDataBaixa );
            $obFormulario->addComponente( $obLblJustificativa );
        }
        $obMontaAtributosConstrucao->geraFormulario( $obFormulario );

        include_once 'FMConsultaImovelConstrucaoListaProcessos.php';
        $obFormulario->addSpan      ( $obSpnProcessoConstrucao          );
        $obFormulario->addSpan      ( $obSpnAtributosProcessoConstrucao );

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $stJs = "d.getElementById('spnConstrucao').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "visualizarProcessoConstrucao":
        $obRCIMConstrucaoOutros = new RCIMConstrucaoOutros;

        $arChaveAtributo = array( "cod_construcao" => $_REQUEST["cod_construcao"], "timestamp" => "'$_REQUEST[timestamp]'", "cod_processo" => $_REQUEST["cod_processo"] );
        $obRCIMConstrucaoOutros->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributo       );
        $obRCIMConstrucaoOutros->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosConstrucao );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo( "Processo" );
        $obLblProcesso->setValue ( $_REQUEST["cod_processo_ano"] );

        if ($_REQUEST['stTipoVinculo'] == "Condomínio") {
            $obRCIMConstrucaoOutros->setCodigoConstrucao( $_REQUEST["cod_construcao"] );
            $obRCIMConstrucaoOutros->setTimestampConstrucao( $_REQUEST['timestamp'] );
            $obRCIMConstrucaoOutros->buscaAreaConstrucaoCondominio( $flAreaConstrucao );
        } else {
            $flAreaConstrucao = $_REQUEST["area"];
        }

        $obLblArea = new Label;
        $obLblArea->setRotulo( "Área da Construção" );
        $obLblArea->setValue ( $flAreaConstrucao );

        $obMontaAtributosConstrucao = new MontaAtributos;
        $obMontaAtributosConstrucao->setTitulo   ( "Atributos da construção" );
        $obMontaAtributosConstrucao->setName     ( "AtributoConstrucao_"     );
        $obMontaAtributosConstrucao->setLabel    ( true                      );
        $obMontaAtributosConstrucao->setRecordSet( $rsAtributosConstrucao    );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso  );
        $obFormularioProcesso->addComponente( $obLblArea      );
        $obMontaAtributosConstrucao->geraFormulario ( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcessoConstrucao').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto( $stJs );
    break;
}

?>
