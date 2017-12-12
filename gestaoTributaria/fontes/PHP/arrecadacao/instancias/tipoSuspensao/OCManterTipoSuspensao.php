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
    * Pagina de processamento para Grupos de Credito
    * Data de Criação   : 25/05/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * $Id: OCManterTipoSuspensao.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.07
*/

/*
$Log$
Revision 1.5  2006/09/15 11:23:59  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeDependente.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php" );

$stCtrl = $_REQUEST['stCtrl'];

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );

switch ($_REQUEST ["stCtrl"]) {
    case "preencheProxCombo":
        $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
        $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
    break;
    case "preencheCombos":
        $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacao->setValorReduzido ( $_REQUEST["stChaveLocalizacao"] );
        $obMontaLocalizacao->preencheCombos();
    break;
    case "buscaLote":
        $obRCIMLote = new RCIMLote;
        $obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
        $obRCIMLote->consultarLote();
        if ( $obRCIMLote->getNumeroLote() ) {
            $stJs = 'd.getElementById("inNumeroLote").innerHTML = "'.$obRCIMLote->getNumeroLote().'";';
        } else {
            $stJs  = 'd.getElementById("inNumeroLote").innerHTML = "&nbsp;";';
            $stJs .= "f.inCodigoLote.value = '';";
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCodigoLote"].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "verificaUnidadeAutonoma":
        $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) );
        $rsUnidadeAutonoma     = new RecordSet;
        $js = "";
        if ($_REQUEST["stImovelCond"]) {
            $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["stImovelCond"]);
            $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveis( $rsImoveis );
            if ( !$rsImoveis->eof() ) {

                $obRCIMUnidadeAutonoma->verificaUnidadeAutonoma( $rsUnidadeAutonoma );

                if ( !$rsUnidadeAutonoma->eof() ) {
                    $inCodigoConstrucaoAutonoma = $rsUnidadeAutonoma->getCampo( "cod_construcao" );
                    $inCodigoTipoAutonoma       = $rsUnidadeAutonoma->getCampo( "cod_tipo"       );
                    $stTipoUnidade              = "Dependente";
                    $js .= "f.hdnCodigoConstrucaoAutonoma.value         = '$inCodigoConstrucaoAutonoma';\n";
                    $js .= "f.hdnCodigoTipoAutonoma.value               = '$inCodigoTipoAutonoma';\n";
                    $js .= "d.getElementById('stTipoUnidade').innerHTML = '$stTipoUnidade';\n";
                    $js .= "f.hdnTipoUnidade.value                      = '$stTipoUnidade';\n";

                    $js .= "d.getElementById('spnNumComp').innerHTML = '';\n";
                } else {
                    $stTipoUnidade                                      = "Autônoma";
                    $js .= "d.getElementById('stTipoUnidade').innerHTML = '$stTipoUnidade';\n";
                    $js .= "f.hdnTipoUnidade.value                      = '$stTipoUnidade';\n";

                    $obTxtNumero = new TextBox;
                    $obTxtNumero->setRotulo          ( "Número"                   );
                    $obTxtNumero->setName            ( "stNumero"                 );
                    $obTxtNumero->setValue           ( $_REQUEST["stNumero"]      );
                    $obTxtNumero->setSize            ( 10                         );
                    $obTxtNumero->setMaxLength       ( 10                         );
                    $obTxtNumero->setNull            ( false                      );

                    $obTxtComplemento = new TextBox;
                    $obTxtComplemento->setRotulo     ( "Complemento"              );
                    $obTxtComplemento->setName       ( "stComplemento"            );
                    $obTxtComplemento->setValue      ( $_REQUEST["stComplemento"] );
                    $obTxtComplemento->setSize       ( 50                         );
                    $obTxtComplemento->setMaxLength  ( 50                         );

                    $obFormulario = new Formulario;
                    $obFormulario->addComponente     ( $obTxtNumero               );
                    $obFormulario->addComponente     ( $obTxtComplemento          );
                    $obFormulario->montaInnerHTML();
                    $js .= "d.getElementById('spnNumComp').innerHTML = '".$obFormulario->getHTML()."';\n";
                }
            } else {
                $js .= "d.getElementById('stTipoUnidade').innerHTML = '';\n";
                $js .= "d.getElementById('spnNumComp').innerHTML = '';\n";
                $js .= "f.hdnTipoUnidade.value = '';\n";
                $js .= "erro = true;\n";
                $js .= "mensagem += 'Inscrição Imobiliária inválida!(".$_REQUEST["stImovelCond"].")';\n";
                $js .= "alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');\n";
            }
        } else {
            $js .= "d.getElementById('stTipoUnidade').innerHTML = '';\n";
            $js .= "d.getElementById('spnNumComp').innerHTML = '';\n";
            $js .= "f.hdnTipoUnidade.value = '';\n";
        }
        SistemaLegado::executaFrameOculto($js);
    break;
    case "habilitaSpnImovelCond":
        $obFormulario = new Formulario;
        if ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
            $obTxtInscricaoMunicipal = new TextBox;
            $obTxtInscricaoMunicipal->setRotulo    ( "Inscrição Imobiliária"           );
            $obTxtInscricaoMunicipal->setTitle     ( "Inscrição imobiliária com a qual a edificação está vinculada" );
            $obTxtInscricaoMunicipal->setName      ( "inInscricaoMunicipal"            );
            $obTxtInscricaoMunicipal->setValue     ( $_REQUEST["inInscricaoMunicipal"] );
            $obTxtInscricaoMunicipal->setSize      ( 8                                 );
            $obTxtInscricaoMunicipal->setMaxLength ( 8                                 );
            $obTxtInscricaoMunicipal->setNull      ( true                              );
            $obTxtInscricaoMunicipal->setInteiro   ( true                              );
            $obFormulario->addComponente           ( $obTxtInscricaoMunicipal          );
        } if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
            $obTxtCondominio = new TextBox;
            $obTxtCondominio->setRotulo            ( "Condomínio"                      );
            $obTxtCondominio->setTitle             ( "Condomínio com o qual a edificação está vinculada" );
            $obTxtCondominio->setName              ( "inCodigoCondominio"              );
            $obTxtCondominio->setValue             ( $_REQUEST["inCodigoCondominio"]   );
            $obTxtCondominio->setSize              ( 8                                 );
            $obTxtCondominio->setMaxLength         ( 8                                 );
            $obTxtCondominio->setNull              ( true                              );
            $obTxtCondominio->setInteiro           ( true                              );
            $obFormulario->addComponente           ( $obTxtCondominio                  );
        }
        $obFormulario->montaInnerHTML();
        $js .= "d.getElementById('spnImovelCond').innerHTML = '".$obFormulario->getHTML()."';\n";
        SistemaLegado::executaFrameOculto($js);
    break;

    case "habilitaSpnNumComp":
        $obFormulario = new Formulario;

        $obTxtNumero = new TextBox;
        $obTxtNumero->setRotulo          ( "Número"                   );
        $obTxtNumero->setName            ( "stNumero"                 );
        $obTxtNumero->setValue           ( $_REQUEST["stNumero"]      );
        $obTxtNumero->setSize            ( 10                         );
        $obTxtNumero->setMaxLength       ( 10                         );
        $obTxtNumero->setNull            ( false                      );

        $obTxtComplemento = new TextBox;
        $obTxtComplemento->setRotulo     ( "Complemento"              );
        $obTxtComplemento->setName       ( "stComplemento"            );
        $obTxtComplemento->setValue      ( $_REQUEST["stComplemento"] );
        $obTxtComplemento->setSize       ( 50                         );
        $obTxtComplemento->setMaxLength  ( 50                         );

        $obFormulario = new Formulario;
        $obFormulario->addComponente     ( $obTxtNumero               );
        $obFormulario->addComponente     ( $obTxtComplemento          );
        $obFormulario->montaInnerHTML();
        $js .= "d.getElementById('spnNumComp').innerHTML = '".$obFormulario->getHTML()."';\n";

        SistemaLegado::executaFrameOculto($js);
    break;

    case "verificaCondominio":
        $obRCIMEdificacao = new RCIMEdificacao;
        $rsCondominio     = new RecordSet;
        $js = "";
        if ($_REQUEST["stImovelCond"]) {
            $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio( $_REQUEST["stImovelCond"]);
            $obRCIMEdificacao->obRCIMCondominio->verificaCondominio ( $rsCondominio            );
            if ( $rsCondominio->eof() ) {
                $js .= "erro = true;\n";
                $js .= "mensagem += 'A Área da Unidade deve ser menor ou igual à Area da Edificação!';\n";
                $js .= "alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');\n";
            }
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case "montaAtributosEdificacao";
        if ($_REQUEST["inCodigoTipo"]) {
            $obRCIMEdificacao = new RCIMEdificacao;
            $obRCIMEdificacao->obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo" => $_REQUEST["inCodigoTipo"] ) );
            $obRCIMEdificacao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosEdifcicacao );

            $obMontaAtributosEdificacao = new MontaAtributos;
            $obMontaAtributosEdificacao->setTitulo     ( "Atributos"              );
            $obMontaAtributosEdificacao->setName       ( "AtributoEdificacao_"    );
            $obMontaAtributosEdificacao->setRecordSet  ( $rsAtributosEdifcicacao );

            $obFormulario = new Formulario;
            $obMontaAtributosEdificacao->geraFormulario ( $obFormulario );
            $obFormulario->montaInnerHTML();

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
            $stHTML = $obFormulario->getHTML();
        } else {
            $stEval = "&nbsp;";
            $stHTML = "&nbsp;";
        }
        $stJs  = "f.stEval.value = '$stEval'; \n";
        $stJs .= "d.getElementById('lsAtributosEdificacao').innerHTML = '".$stHTML."';";
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "buscaCondominio":
        $obRCIMCondominio  = new RCIMCondominio;
        if ($_POST['stImovelCond'] != '') {
            $obRCIMCondominio->setCodigoCondominio( $_POST['stImovelCond']  );
            $obErro = $obRCIMCondominio->consultarCondominio( $rsCondominio );
            if ( $obErro->ocorreu() or $rsCondominio->eof() ) {
                $stJs .= 'f.stImovelCond.value = "";';
                $stJs .= 'f.stImovelCond.focus();';
                $stJs .= 'd.getElementById("campoInnerCond").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Condomínio não encontrado. (".$_POST["stImovelCond"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs  = 'd.getElementById("campoInnerCond").innerHTML = "'.$rsCondominio->getCampo('nom_condominio').'";';
            }
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "MontaListaUnidadesDependentes":
        $obRCIMUnidadeDependente = new RCIMUnidadeDependente( new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) ) );
        $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $_REQUEST["hdnCodigoTipo"] );
        $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucao"] );
        $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["stImovelCond"] );
        $obRCIMUnidadeDependente->listarUnidadesDependentes( $rsUnidadesDependentes );

        $obLista = new Lista();
        $obLista->setTitulo( "Unidades Dependentes" );
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsUnidadesDependentes );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Tipo" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Área" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Imóvel" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Selecionar" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_construcao_dependente" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_tipo" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "area" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inscricao_municipal" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obRdnUnidadeSelecionada = new Radio();
        $obRdnUnidadeSelecionada->setValue( "cod_construcao_dependente" );
        $obRdnUnidadeSelecionada->setName( "boUnidadeSelecionada" );
        $obRdnUnidadeSelecionada->setNull( false );

        $obLista->addDadoComponente( $obRdnUnidadeSelecionada, false );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
        if ( $rsUnidadesDependentes->getNumLinhas() > 0 ) {
            $stJs .= "f.hdnListaDependentes.value = true;";
        }
        $stJs .= "d.getElementById('spnUnidadesDependentes').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "MontaListaSelecionarEdificacao":
        $obRCIMEdificacao =  new RCIMEdificacao();
        if ($_REQUEST["hdnVinculoEdificacao"] == "Condomínio") {
            $obRCIMEdificacao->listarEdificacoes( $rsEdificacao );
        } else {
            $obRCIMEdificacao->listarEdificacoesImovel( $rsEdificacao );
        }

        $obLista = new Lista();
        $obLista->setTitulo( "Registros de edificação" );
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsEdificacao );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Tipo" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Área" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( $_REQUEST["hdnVinculoEdificacao"] );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Selecionar" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_construcao" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_tipo" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "area_real" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obLista->addDado();
        if ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
            $obLista->ultimoDado->setCampo( "inscricao_municipal" );
        } else {
            $obLista->ultimoDado->setCampo( "nom_condominio" );
        }
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obRdnEdificacaoSelecionada = new Radio();
        $obRdnEdificacaoSelecionada->setValue( "[cod_construcao]-[area_real]" );
        $obRdnEdificacaoSelecionada->setName( "boEdificacaoSelecionada" );
        $obRdnEdificacaoSelecionada->setNull( false );

        $obLista->addDadoComponente( $obRdnEdificacaoSelecionada, false );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
        $stJs .= "d.getElementById('spnSelecionarEdificacao').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto($stJs);
    break;

   case "visualizarProcesso":
        $obRCIMEdificacao     = new RCIMEdificacao;

        $arChaveAtributoEdificacaoProcesso =  array("cod_tipo" => $_REQUEST["cod_tipo"], "cod_construcao" => $_REQUEST["cod_construcao"],"timestamp" => "'$_REQUEST[timestamp]'", "cod_processo" => $_REQUEST["cod_processo"] );
        $obRCIMEdificacao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoEdificacaoProcesso );
        $obRCIMEdificacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosEdificacaoProcesso );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo    ( "Processo" );
        $obLblProcesso->setValue     ( str_pad($_REQUEST["cod_processo"],5,"0",STR_PAD_LEFT) . "/" . $_REQUEST["ano_exercicio"]  );

        $obMontaAtributosEdificacaoProcesso = new MontaAtributos;
        $obMontaAtributosEdificacaoProcesso->setTitulo     ( "Atributos"        );
        $obMontaAtributosEdificacaoProcesso->setName       ( "Atributo_"  );
        $obMontaAtributosEdificacaoProcesso->setLabel       ( true  );
        $obMontaAtributosEdificacaoProcesso->setRecordSet  ( $rsAtributosEdificacaoProcesso );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso  );
        $obMontaAtributosEdificacaoProcesso->geraFormulario ( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcesso').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto($stJs);

    break;

}
?>
