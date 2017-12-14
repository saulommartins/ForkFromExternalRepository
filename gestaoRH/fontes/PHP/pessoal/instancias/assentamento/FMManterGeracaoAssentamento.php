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
 * Página de Formulario para Geração de Assentamento para cada servidor
 * Data de Criação: 19/01/2006

 * @author Analista: Vandré Miguel Ramos
 * @author Desenvolvedor: Andre Almeida

 * @ignore

 $Id: FMManterGeracaoAssentamento.php 66364 2016-08-17 21:11:39Z michel $

 * Casos de uso: uc-04.04.14
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RPessoalGeracaoAssentamento.class.php";
include_once CAM_GRH_PES_NEGOCIO."RPessoalClassificacaoAssentamento.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php";

?>

<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/json-js-master/json2.js" type="text/javascript"></script>

<?php
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$stLink  = '&inCodLotacao='      .$request->get('inCodLotacao');
$stLink .= '&inCodAssentamento=' .$request->get('inCodAssentamento');
$stLink .= '&inContrato='        .$request->get('inContrato');
$stLink .= '&boCargoExercido='   .$request->get('boCargoExercido');
$stLink .= '&inCodCargo='        .$request->get('inCodCargo');
$stLink .= '&inCodEspecialidade='.$request->get('inCodEspecialidade');
$stLink .= '&boFuncaoExercida='  .$request->get('boFuncaoExercida');
$stLink .= '&stDataInicial='     .$request->get('stDataInicial');
$stLink .= '&stDataFinal='       .$request->get('stDataFinal');
$stLink .= '&stModoGeracao='     .$request->get('stModoGeracao');
$stLink .= "&dtInicial="         .$request->get('dt_inicial');
$stLink .= "&dtFinal="           .$request->get('dt_final');
$stLink .= "&stAcao=".$stAcao;
$stLink .= "&pg=".$request->get('pg');
$stLink .= "&pos=".$request->get('pos');

$arLink = Sessao::read('link');

//Define o nome dos arquivos PHP
$stPrograma = "ManterGeracaoAssentamento";
$pgForm     = "FM".$stPrograma.".php";
$pgFilt     = "FL".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php"."?".Sessao::getId().$stLink."&pg=".$arLink["pg"]."&pos=".$arLink["pos"];
$pgOcul     = "OC".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );
$obRPessoalGeracaoAssentamento  = new RPessoalGeracaoAssentamento();

include_once ($pgJS);
include_once ($pgOcul);

//Instancia o formulário
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );
$obForm->setEncType ( "multipart/form-data" );

//Define o objeto de controle
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

if ($stAcao == "excluir" or $stAcao == "alterar" or $stAcao == "consultar") {
    $arFiltros['inCodAssentamentoGerado'] = $_REQUEST['inCodAssentamentoGerado'];
    $obRPessoalGeracaoAssentamento->listarAssentamentoServidor($rsGeracaoAssentamento,$arFiltros);
    $inRegistro                 = $rsGeracaoAssentamento->getCampo('registro');
    $stCgm                      = $rsGeracaoAssentamento->getCampo('numcgm')."-".$rsGeracaoAssentamento->getCampo('nom_cgm');
    $inCodClassificacao         = $rsGeracaoAssentamento->getCampo('cod_classificacao');
    $_POST["inCodClassificacao"]= $inCodClassificacao;
    $stClassificacao            = $rsGeracaoAssentamento->getCampo('descricao_classificacao');
    $inCodAssentamento          = $rsGeracaoAssentamento->getCampo('cod_assentamento');
    $_POST["inCodAssentamento"] = $inCodAssentamento;
    $stAssentamento             = $rsGeracaoAssentamento->getCampo('descricao_assentamento');
    $inCodAssentamentoGerado    = $rsGeracaoAssentamento->getCampo('cod_assentamento_gerado');
    $stTimestamp                = $rsGeracaoAssentamento->getCampo('timestamp');
    $dtPeriodoInicial           = $rsGeracaoAssentamento->getCampo('periodo_inicial');
    $dtPeriodoFinal             = $rsGeracaoAssentamento->getCampo('periodo_final');
    $_REQUEST["dtInicial"]      = $rsGeracaoAssentamento->getCampo('dt_inicial');
    $_REQUEST["dtFinal"]        = $rsGeracaoAssentamento->getCampo('dt_final');
    $stObservacao               = $rsGeracaoAssentamento->getCampo('observacao');

    if ($stAcao != "alterar") {
        $stObservacao = str_replace("\n","<br>",$stObservacao);
    }
    $rsAssentamentoNorma = new RecordSet();

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoNorma.class.php");
    $obTPessoalAssentamentoGeradoNorma = new TPessoalAssentamentoGeradoNorma();
    $stFiltro  = " AND cod_assentamento_gerado = ".$_REQUEST['inCodAssentamentoGerado'];
    $stFiltro .= " AND timestamp = '".$rsGeracaoAssentamento->getCampo('timestamp')."'";
    $obTPessoalAssentamentoGeradoNorma->recuperaRelacionamento($rsAssentamentoNorma,$stFiltro);

    while (!$rsAssentamentoNorma->eof()) {
        $arNorma['inCodNorma'] = $rsAssentamentoNorma->getCampo("cod_norma");
        $arNorma['inCodTipoNorma'] = $rsAssentamentoNorma->getCampo("cod_tipo_norma");

        $arNormas[] = $arNorma;

        $rsAssentamentoNorma->proximo();
    }
    Sessao::write("arCodNorma", $arNormas);

    $stPeriodo          = $dtPeriodoInicial ." a ". $dtPeriodoFinal;
    $stPeriodoLicPremio = $rsGeracaoAssentamento->getCampo('dt_inicial') ." a ".$rsGeracaoAssentamento->getCampo('dt_final');
    Sessao::write('inCodClassificacao', $inCodClassificacao);
    Sessao::write('inCodAssentamento', $inCodAssentamento);
    Sessao::write('stDataInicial', $dtPeriodoInicial);
    Sessao::write('stDataFinal', $dtPeriodoFinal);

    include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoArquivoDigital.class.php";
    $obTPessoalAssentamentoArquivoDigital = new TPessoalAssentamentoArquivoDigital();
    $obTPessoalAssentamentoArquivoDigital->setDado('cod_assentamento_gerado' , $inCodAssentamentoGerado);
    $obTPessoalAssentamentoArquivoDigital->setDado('cod_contrato'            , $rsGeracaoAssentamento->getCampo('cod_contrato'));
    $obTPessoalAssentamentoArquivoDigital->recuperaAssentamentoArquivoDigital($rsArquivoDigital);

    $arArquivosDigitais = array();
    $inId = 0;
    $inIdAssentamento = 0;
    $stDirANEXO = CAM_GRH_PESSOAL."anexos/";
    foreach($rsArquivoDigital->getElementos() AS $chave => $arquivo){
        $arArquivosUpload['inIdAssentamento']    = $inIdAssentamento;
        $arArquivosUpload['stModoGeracao']       = 'contrato';
        $arArquivosUpload['stArquivo']           = $stDirANEXO.$arquivo['arquivo_digital'];
        $arArquivosUpload['arquivo_digital']     = $arquivo['arquivo_digital'];
        $arArquivosUpload['name']                = $arquivo['nome_arquivo'];
        $arArquivosUpload['inId']                = $inId;
        $arArquivosUpload['boCopiado']           = 'TRUE';
        $arArquivosUpload['tmp_name']            = $stDirANEXO.$arquivo['arquivo_digital'];
        $arArquivosUpload['inChave']             = $arquivo['cod_contrato'];
        $arArquivosUpload['inCodClassificacao']  = $inCodClassificacao;
        $arArquivosUpload['inCodAssentamento']   = $arquivo['cod_assentamento'];
        $arArquivosUpload['stDataInicial']       = $arquivo['periodo_inicial'];
        $arArquivosUpload['stDataFinal']         = $arquivo['periodo_final'];
        $arArquivosUpload['boExcluido']          = 'FALSE';

        $arArquivosDigitais[] = $arArquivosUpload;

        $inId++;
    }

    $arAssentamentoAtual = array();
    $arAssentamentoAtual['stModoGeracao']      = 'contrato';
    $arAssentamentoAtual['inChave']            = $rsGeracaoAssentamento->getCampo('cod_contrato');
    $arAssentamentoAtual['stNomeChave']        = 'inCodContrato';
    $arAssentamentoAtual['inCodClassificacao'] = $inCodClassificacao;
    $arAssentamentoAtual['inCodAssentamento']  = $inCodAssentamento;
    $arAssentamentoAtual['stDataInicial']      = $dtPeriodoInicial;
    $arAssentamentoAtual['stDataFinal']        = $dtPeriodoFinal;
    $arAssentamentoAtual['inIdAssentamento']   = $inIdAssentamento;

    Sessao::write("inId", $inIdAssentamento);
    Sessao::write("arArquivosDigitais", $arArquivosDigitais);    
    Sessao::write("arAssentamentoAtual", $arAssentamentoAtual);

    $obHdnCodAssentamentoGerado = new Hidden;
    $obHdnCodAssentamentoGerado->setName  ( "inCodAssentamentoGerado" );
    $obHdnCodAssentamentoGerado->setValue ( $inCodAssentamentoGerado  );

    $obHdnCodAssentamento = new Hidden;
    $obHdnCodAssentamento->setName  ( "inCodAssentamento" );
    $obHdnCodAssentamento->setValue ( $inCodAssentamento  );

    $obHdnTimestamp = new Hidden;
    $obHdnTimestamp->setName  ( "stTimestamp" );
    $obHdnTimestamp->setValue ( $stTimestamp  );

    $obLblCgm = new Label;
    $obLblCgm->setRotulo ( "CGM"  );
    $obLblCgm->setValue  ( $stCgm );

    $obLblContrato = new Label;
    $obLblContrato->setRotulo ( "Matrícula"  );
    $obLblContrato->setValue  ( $inRegistro  );

    $obHdnContrato = new Hidden;
    $obHdnContrato->setName  ( "inRegistro" );
    $obHdnContrato->setValue ( $inRegistro  );

    if ($stAcao == "excluir" or $stAcao == "consultar") {
        $obLblClassificacao = new Label;
        $obLblClassificacao->setRotulo          ( "Classificacao"   );
        $obLblClassificacao->setValue           ( $stClassificacao  );

        $obLblAssentamento = new Label;
        $obLblAssentamento->setRotulo           ( "Assentamento"  );
        $obLblAssentamento->setValue            ( $stAssentamento );

        $obLblQuantidadeDias = new Label;
        $obLblQuantidadeDias->setRotulo         ( "Quantidade (Dias)" );
        $obLblQuantidadeDias->setValue          ( $inQuantidadeDias   );
        $obLblQuantidadeDias->setId             ( "inQuantidadeDias"  );

        $obLblPeriodo = new Label;
        $obLblPeriodo->setRotulo                ( "Período"   );
        $obLblPeriodo->setValue                 ( $stPeriodo  );

        $obLblPeriodoLicPremio = new Label;
        $obLblPeriodoLicPremio->setRotulo       ( "Período Aquisitivo Licença Prêmio" );
        $obLblPeriodoLicPremio->setValue        ( $stPeriodoLicPremio                 );

        $obLblObservacao = new Label;
        $obLblObservacao->setRotulo             ( "Observação"  );
        $obLblObservacao->setValue              ( $stObservacao );

        $obTxtMotivoExclusao = new TextArea;
        $obTxtMotivoExclusao->setRotulo         ( "Motivo da Exclusão"                );
        $obTxtMotivoExclusao->setName           ( "stMotivoExclusao"                  );
        $obTxtMotivoExclusao->setId             ( "stMotivoExclusao"                  );
        $obTxtMotivoExclusao->setValue          ( $stMotivoExclusao                   );
        $obTxtMotivoExclusao->setTitle          ( "Informe o motivo para a exclusão." );
        $obTxtMotivoExclusao->setNull           ( false                               );
    }

    if ($stAcao == "consultar") {
        $obBtnVoltar = new Button();
        $obBtnVoltar->setName("btnVoltar");
        $obBtnVoltar->setValue("Voltar");
        $obBtnVoltar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().$stLink."','telaPrincipal');");

        $obBtnFiltro = new Button();
        $obBtnFiltro->setName("btnFiltro");
        $obBtnFiltro->setValue("Filtro");
        $obBtnFiltro->obEvento->setOnClick("Cancelar('".$pgFilt.'?'.Sessao::getId()."&stAcao=".$stAcao."','telaPrincipal');");
    }

}

if ($stAcao == "incluir" or $stAcao == "alterar") {
    $rsClassificacao = new RecordSet;
    if ($stAcao == "alterar") {
        //Para montar o combo Classificao
        $obRPessoalClassificacaoAssentamento = new RPessoalClassificacaoAssentamento();
        $obRPessoalClassificacaoAssentamento->listarClassificacaoGeracaoAssentamento( $rsClassificacao, $inRegistro, $comboType );
    }

    //Define objeto TEXTBOX para informar o CODIGO da classificação
    $obTxtCodClassificao = new TextBox;
    $obTxtCodClassificao->setRotulo                 ( "*Classificação"                           );
    $obTxtCodClassificao->setTitle                  ( "Informe a classificação do assentamento." );
    $obTxtCodClassificao->setName                   ( "inCodClassificacaoTxt"                    );
    $obTxtCodClassificao->setId                     ( "inCodClassificacaoTxt"                    );
    $obTxtCodClassificao->setSize                   ( 10                                         );
    $obTxtCodClassificao->setMaxLength              ( 10                                         );
    $obTxtCodClassificao->setInteiro                ( true                                       );
    $obTxtCodClassificao->setValue                  ( isset($inCodClassificacao) ? $inCodClassificacao : '' );
    $obTxtCodClassificao->obEvento->setOnChange     ( "buscaValor('preencherAssentamento');"     );
    $obTxtCodClassificao->obEvento->setOnBlur       ( "validaObrigatorios();"                    );

    //Define objeto SELECT para listar a DESCRIÇÂO da classificação
    $obCmbClassificacao = new Select;
    $obCmbClassificacao->setRotulo                    ( "*Classificação"                                      );
    $obCmbClassificacao->setTitle                     ( "Informe a classificação do assentamento."            );
    $obCmbClassificacao->setName                      ( "inCodClassificacao"                                  );
    $obCmbClassificacao->setId                        ( "inCodClassificacao"                                  );
    $obCmbClassificacao->setStyle                     ( "width: 200px"                                        );
    $obCmbClassificacao->addOption                    ( "", "Selecione"                                       );
    $obCmbClassificacao->setValue                     ( isset($inCodClassificacao) ? $inCodClassificacao : '' );
    $obCmbClassificacao->setCampoID                   ( "cod_classificacao"                                   );
    $obCmbClassificacao->setCampoDesc                 ( "descricao"                                           );
    $obCmbClassificacao->preencheCombo                ( $rsClassificacao                                      );
    $obCmbClassificacao->obEvento->setOnChange        ( "buscaValor('preencherAssentamento');"                );
    $obCmbClassificacao->obEvento->setOnBlur          ( "validaObrigatorios();"                               );

    //Define objeto TEXTBOX para informar o CODIGO da classificação
    $obTxtCodAssentamento = new TextBox;
    $obTxtCodAssentamento->setRotulo                ( "*Assentamento"                                     );
    $obTxtCodAssentamento->setTitle                 ( "Informe o assentamento a ser gerado."              );
    $obTxtCodAssentamento->setName                  ( "inCodAssentamentoTxt"                              );
    $obTxtCodAssentamento->setId                    ( "inCodAssentamentoTxt"                              );
    $obTxtCodAssentamento->setSize                  ( 10                                                  );
    $obTxtCodAssentamento->setMaxLength             ( 10                                                  );
    $obTxtCodAssentamento->setInteiro               ( true                                                );
    $obTxtCodAssentamento->setValue                 ( isset($inCodAssentamento) ? $inCodAssentamento : '' );
    $obTxtCodAssentamento->obEvento->setOnChange    ( "BloqueiaFrames(true,false); buscaValor('processarQuantDiasAssentamento');" );
    $obTxtCodAssentamento->obEvento->setOnBlur      ( "validaObrigatorios();"                             );

    //Define objeto SELECT para listar a DESCRIÇÂO do motivo
    $obCmbAssentamento = new Select;
    $obCmbAssentamento->setRotulo                   ( "*Assentamento"                                     );
    $obCmbAssentamento->setTitle                    ( "Informe o assentamento a ser gerado."              );
    $obCmbAssentamento->setName                     ( "inCodAssentamento"                                 );
    $obCmbAssentamento->setId                       ( "inCodAssentamento"                                 );
    $obCmbAssentamento->setStyle                    ( "width: 200px"                                      );
    $obCmbAssentamento->addOption                   ( "", "Selecione"                                     );
    $obCmbAssentamento->setValue                    ( isset($inCodAssentamento) ? $inCodAssentamento : '' );
    $obCmbAssentamento->obEvento->setOnChange       ( "BloqueiaFrames(true,false); buscaValor('processarQuantDiasAssentamento');" );
    $obCmbAssentamento->obEvento->setOnBlur         ( "validaObrigatorios();"                             );

    //Define objeto TEXTBOX para armazenar a DESCRICAO do modelo
    $obTxtQuantidadeDias = new TextBox;
    $obTxtQuantidadeDias->setRotulo                 ( "Quantidade (Dias)"                               );
    $obTxtQuantidadeDias->setTitle                  ( "Informe a quantidade de dias referentes à ocorrência do assentamento." );
    $obTxtQuantidadeDias->setName                   ( "inQuantidadeDias"                                );
    $obTxtQuantidadeDias->setId                     ( "inQuantidadeDias"                                );
    $obTxtQuantidadeDias->setValue                  ( isset($inQuantidadeDias) ? $inQuantidadeDias : '' );
    $obTxtQuantidadeDias->setSize                   ( 4                                                 );
    $obTxtQuantidadeDias->setMaxLength              ( 8                                                 );
    $obTxtQuantidadeDias->setInteiro                ( true                                              );
    $obTxtQuantidadeDias->obEvento->setOnChange     ( "buscaValor('processarTriadi1');"                 );

    $obDataInicial = new Data;
    $obDataInicial->setName               ( "stDataInicial" );
    $obDataInicial->setId                 ( "stDataInicial" );
    $obDataInicial->setRotulo             ( "*Período" );
    $obDataInicial->setValue              ( isset($dtPeriodoInicial) ? $dtPeriodoInicial : '' );
    $obDataInicial->setTitle              ( "Informe o período do assentamento." );
    $obDataInicial->setNull               (true);
    $obDataInicial->obEvento->setOnChange ( "buscaValor('processarTriadi2');" );
    $obDataInicial->obEvento->setOnBlur   ( "validaObrigatorios();" );

    $obLabelAte = new Label;
    $obLabelAte->setRotulo  ( "*Período"                           );
    $obLabelAte->setValue   ( "até"                                );
    $obLabelAte->setTitle   ( "Informe o período do assentamento." );

    $obDataFinal = new Data;
    $obDataFinal->setName               ( "stDataFinal"                                 );
    $obDataFinal->setId                 ( "stDataFinal"                                 );
    $obDataFinal->setRotulo             ( "*Período"                                    );
    $obDataFinal->setValue              ( isset($dtPeriodoFinal) ? $dtPeriodoFinal : '' );
    $obDataFinal->setTitle              ( "Informe o período do assentamento."          );
    $obDataFinal->obEvento->setOnChange ( "buscaValor('processarTriadi3');"             );
    $obDataFinal->obEvento->setOnBlur   ( "validaObrigatorios();"                       );

    $obObservacao = new TextArea;
    $obObservacao->setRotulo               ( "*Observação"                                              );
    $obObservacao->setName                 ( "stObservacao"                                             );
    $obObservacao->setId                   ( "stObservacao"                                             );
    $obObservacao->setValue                ( isset($stObservacao) ? $stObservacao : '' );
    $obObservacao->setTitle                ( "Informe a observação da geração do assentamento."         );
    $obObservacao->obEvento->setOnKeyPress (" return validaMaxCaracterQuebraLinha(this,1800,event,0); " );
    $obObservacao->obEvento->setOnBlur     (" return validaMaxCaracterQuebraLinha(this,1800,event,1); " );

    $obTipoNormaNorma = new IBuscaInnerNorma(false,true);

    $obFileArqDigital = new FileBox;
    $obFileArqDigital->setId     ( "stArqDigital"         );
    $obFileArqDigital->setName   ( "stArqDigital"         );
    $obFileArqDigital->setValue  ( ""                     );
    $obFileArqDigital->setRotulo ( "Arquivo Digital "     );
    $obFileArqDigital->setTitle  ( "Selecione o arquivo." );

    $stTamLimite = ini_get("upload_max_filesize");
    switch (substr($stTamLimite, -1)){
        case 'M': case 'm':
            $stTamLimite = (int)(((int) $stTamLimite * 1048576)/1000000).'Mb';
        break;
        case 'K': case 'k':
            $stTamLimite = (int)(((int) $stTamLimite * 1024)/1000).'Kb';
        break;
        case 'G': case 'g':
            $stTamLimite = (int)(((int) $stTamLimite * 1073741824)/1000000000).'Gb';
        break;
    }

    $stArqValidos  = "JPG, JPEG, GIF, PNG, ODT, DOC e DOCX. ";
    $stArqValidos .= "Tamanho Limite: ".$stTamLimite.". ";

    $obLblArqValidos = new Label;
    $obLblArqValidos->setRotulo ( "Tipos de Arquivos Válidos");
    $obLblArqValidos->setValue ( $stArqValidos  );

    $obBtnIncluirArqDigital = new Button;
    $obBtnIncluirArqDigital->setValue             ( "Incluir arquivo"  );
    $obBtnIncluirArqDigital->setName              ( "incluiArqDigital" );
    $obBtnIncluirArqDigital->setId                ( "incluiArqDigital" );
    $obBtnIncluirArqDigital->obEvento->setOnClick ( "BloqueiaFrames(true,false); buscaValor('incluirArqDigital');" );
}

//Span da Listagem de Arquivos Digitais
$obSpnListaArqDigital = new Span;
$obSpnListaArqDigital->setID("spnListaArqDigital");

$obSpnNormasFundamentacaoLegal = new Span();
$obSpnNormasFundamentacaoLegal->setId("spnFundamentacaoLegal");

$obBtnIncluirNorma = new Button;
$obBtnIncluirNorma->setName                          ( "btIncluirNorma"                                                       );
$obBtnIncluirNorma->setId                            ( "btIncluirNorma"                                                       );
$obBtnIncluirNorma->setValue                         ( "Incluir"                                                              );
$obBtnIncluirNorma->obEvento->setOnClick             ( "buscaValor('incluirNorma');"                                          );
$obBtnIncluirNorma->setTitle                         ( "Clique para incluir a norma na lista de Normas/Fundamentação Legal"   );

$obSpnLicencaPremio = new Span();
$obSpnLicencaPremio->setId("spnLicencaPremio");

$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

switch ($stAcao) {
    case "alterar":
        $obHdnModoGeracao = new Hidden;
        $obHdnModoGeracao->setName           ( "stModoGeracao"                                  );
        $obHdnModoGeracao->setValue          ( 'contrato'                                       );

        $obSpanCargoFuncaoSalario = new Span();
        $obSpanCargoFuncaoSalario->setId('spnCargoFuncaoSalario');

        $obFormulario->addHidden             ( $obHdnCodAssentamentoGerado                      );
        $obFormulario->addHidden             ( $obHdnModoGeracao                                );
        $obFormulario->addTitulo             ( "Geração do Assentamento"                        );
        $obFormulario->addComponente         ( $obLblCgm                                        );
        $obFormulario->addComponente         ( $obLblContrato                                   );
        $obFormulario->addHidden             ( $obHdnContrato                                   );
        $obFormulario->addTitulo             ( "Informações do Assentamento"                    );
        $obFormulario->addComponenteComposto ( $obTxtCodClassificao , $obCmbClassificacao       );
        $obFormulario->addComponenteComposto ( $obTxtCodAssentamento, $obCmbAssentamento        );
        $obFormulario->addComponente         ( $obTxtQuantidadeDias                             );
        $obFormulario->agrupaComponentes     ( array($obDataInicial, $obLabelAte, $obDataFinal) );
        $obFormulario->addSpan               ( $obSpnLicencaPremio                              );
        $obTipoNormaNorma->geraFormulario    ( $obFormulario                                    );
        $obFormulario->addComponente         ( $obBtnIncluirNorma                               );
        $obFormulario->addSpan               ( $obSpnNormasFundamentacaoLegal                   );
        $obFormulario->addComponente         ( $obLblArqValidos                                 );
        $obFormulario->addComponente         ( $obFileArqDigital                                );
        $obFormulario->addComponente         ( $obBtnIncluirArqDigital                          );
        $obFormulario->addSpan               ( $obSpnListaArqDigital                            );
        $obFormulario->addSpan               ( $obSpanCargoFuncaoSalario                        );
        $obFormulario->addComponente         ( $obObservacao                                    );
        $obFormulario->Cancelar              ( $pgList.'?'.Sessao::getId().$stLink              );
    break;

    case "incluir":
        $obHdnEval = new HiddenEval;
        $obHdnEval->setName  ( "stEval" );
        $obHdnEval->setValue ( ""       );

        $obHdnModoGeracao = new Hidden;
        $obHdnModoGeracao->setName  ( "hdnModoGeracao"  );
        $obHdnModoGeracao->setValue ( "contrato"        );

        $obHdnCodContrato = new Hidden;
        $obHdnCodContrato->setName  ( "inCodContrato"  );
        $obHdnCodContrato->setValue ( ""               );

        $obHdnCodMatricula = new Hidden;
        $obHdnCodMatricula->setName  ( "inCodMatricula" );
        $obHdnCodMatricula->setValue ( ""               );

        $obSpanCargoFuncaoSalario = new Span();
        $obSpanCargoFuncaoSalario->setId('spnCargoFuncaoSalario');

        //Define objeto SELECT para informar o modo que vai ser gerado o assentamento.
        $obCmbModoGeracao = new Select;
        $obCmbModoGeracao->setRotulo             ( "Gerar Assentamento"                             );
        $obCmbModoGeracao->setName               ( "stModoGeracao"                                  );
        $obCmbModoGeracao->setId                 ( "stModoGeracao"                                  );
        $obCmbModoGeracao->setTitle              ( "Informe o modo que o assentamento será gerado." );
        $obCmbModoGeracao->addOption             ( "contrato"    , "Matrícula"                      );
        $obCmbModoGeracao->addOption             ( "cgm/contrato", "CGM/Matrícula"                  );
        $obCmbModoGeracao->addOption             ( "cargo"       , "Cargo"                          );
        $obCmbModoGeracao->addOption             ( "lotacao"     , "Lotação"                        );
        $obCmbModoGeracao->setValue              ( "contrato"                                       );
        $obCmbModoGeracao->setNull               ( false                                            );
        $obCmbModoGeracao->obEvento->setOnChange ( "buscaValor('gerarAssentamento');"               );

        $obSpan1 = new Span;
        $obSpan1->setId ( "spnSpan1" );

        $obBtnIncluir = new Button;
        $obBtnIncluir->setName              ( "btIncluir"                          );
        $obBtnIncluir->setId                ( "btIncluir"                          );
        $obBtnIncluir->setValue             ( "Incluir"                            );
        $obBtnIncluir->obEvento->setOnClick ( "buscaValor('incluirAssentamento');" );
        $obBtnIncluir->setDisabled          ( true                                 );
        $obBtnIncluir->setTitle             ( "Clique para incluir o assentamento para o contrato" );

        $obBtnAlterar = new Button;
        $obBtnAlterar->setName              ( "btAlterar"                          );
        $obBtnAlterar->setId                ( "btAlterar"                          );
        $obBtnAlterar->setValue             ( "Alterar"                            );
        $obBtnAlterar->obEvento->setOnClick ( "buscaValor('alterarAssentamento');" );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName              ( "btLimpar"                          );
        $obBtnLimpar->setId                ( "btLimpar"                          );
        $obBtnLimpar->setValue             ( "Limpar"                            );
        $obBtnLimpar->obEvento->setOnClick ( "buscaValor('limparAssentamento');" );

        $obSpan2 = new Span;
        $obSpan2->setId ( "spnSpan2" );

        $obFormulario->addHidden              ( $obHdnEval , true                                 );
        $obFormulario->addHidden              ( $obHdnModoGeracao                                 );
        $obFormulario->addHidden              ( $obHdnCodContrato                                 );        
        $obFormulario->addHidden              ( $obHdnCodMatricula                                );
        $obFormulario->addTitulo              ( "Geração do Assentamento"                         );
        $obFormulario->addComponente          ( $obCmbModoGeracao                                 );
        $obFormulario->addSpan                ( $obSpan1                                          );
        $obFormulario->addTitulo              ( "Informações do Assentamento"                     );
        $obFormulario->addComponenteComposto  ( $obTxtCodClassificao , $obCmbClassificacao        );
        $obFormulario->addComponenteComposto  ( $obTxtCodAssentamento, $obCmbAssentamento         );
        $obFormulario->addComponente          ( $obTxtQuantidadeDias                              );
        $obFormulario->agrupaComponentes      ( array($obDataInicial, $obLabelAte, $obDataFinal)  );
        $obFormulario->addSpan                ( $obSpnLicencaPremio                               );
        $obTipoNormaNorma->geraFormulario     ( $obFormulario                                     );
        $obFormulario->addComponente          ( $obBtnIncluirNorma                                );
        $obFormulario->addSpan                ( $obSpnNormasFundamentacaoLegal                    );
        $obFormulario->addSpan                ( $obSpanCargoFuncaoSalario                         );
        $obFormulario->addComponente          ( $obLblArqValidos                                  );
        $obFormulario->addComponente          ( $obFileArqDigital                                 );
        $obFormulario->addComponente          ( $obBtnIncluirArqDigital                           );
        $obFormulario->addSpan                ( $obSpnListaArqDigital                             );
        $obFormulario->addComponente          ( $obObservacao                                     );
        $obFormulario->agrupaComponentes      ( array($obBtnIncluir, $obBtnAlterar, $obBtnLimpar) );
        $obFormulario->addSpan                ( $obSpan2                                          );

        $obBtnLimparCampos = new Button;
        $obBtnLimparCampos->setName              ( "btnLimparCampos"                 );
        $obBtnLimparCampos->setValue             ( "Limpar"                          );
        $obBtnLimparCampos->setTipo              ( "button"                          );
        $obBtnLimparCampos->obEvento->setOnClick ( "buscaValor('limparFormulario');" );
        $obBtnLimparCampos->setDisabled          ( false                             );

        $obBtnOK = new Ok;
        $obBtnOK->obEvento->setOnClick ( "buscaValor('submeter');" );

        $obFormulario->defineBarra ( array($obBtnOK,$obBtnLimparCampos) );

        $obFormulario->setFormFocus ( $obCmbModoGeracao->getId()        );
    break;

    case "excluir":
        $obFormulario->addHidden     ( $obHdnCodAssentamentoGerado   );
        $obFormulario->addHidden     ( $obHdnCodAssentamento         );
        $obFormulario->addHidden     ( $obHdnTimestamp               );
        $obFormulario->addHidden     ( $obHdnContrato                );
        $obFormulario->addTitulo     ( "Geração do Assentamento"     );
        $obFormulario->addComponente ( $obLblCgm                     );
        $obFormulario->addComponente ( $obLblContrato                );
        $obFormulario->addTitulo     ( "Informações do Assentamento" );
        $obFormulario->addComponente ( $obLblClassificacao           );
        $obFormulario->addComponente ( $obLblAssentamento            );
        $obFormulario->addComponente ( $obLblQuantidadeDias          );
        $obFormulario->addComponente ( $obLblPeriodo                 );
        if ($rsGeracaoAssentamento->getCampo('dt_inicial') != "" and $rsGeracaoAssentamento->getCampo('dt_final') != "") {
            $obFormulario->addComponente ( $obLblPeriodoLicPremio  );
        }
        $obFormulario->addSpan       ( $obSpnNormasFundamentacaoLegal  );
        $obFormulario->addSpan       ( $obSpnListaArqDigital           );
        $obFormulario->addComponente ( $obLblObservacao  );
        $obFormulario->addComponente ( $obTxtMotivoExclusao  );
        $obFormulario->Cancelar      ( $pgList.'?'.Sessao::getId().$stLink );

    break;

    case "consultar":
        $obFormulario->addTitulo     ( "Geração do Assentamento"     );
        $obFormulario->addComponente ( $obLblCgm                     );
        $obFormulario->addComponente ( $obLblContrato                );
        $obFormulario->addTitulo     ( "Informações do Assentamento" );
        $obFormulario->addComponente ( $obLblClassificacao           );
        $obFormulario->addComponente ( $obLblAssentamento            );
        $obFormulario->addComponente ( $obLblQuantidadeDias          );
        $obFormulario->addComponente ( $obLblPeriodo                 );
        if ($rsGeracaoAssentamento->getCampo('dt_inicial') != "" and $rsGeracaoAssentamento->getCampo('dt_final') != "") {
            $obFormulario->addComponente ( $obLblPeriodoLicPremio );
        }
        $obFormulario->addSpan       ( $obSpnNormasFundamentacaoLegal  );
        $obFormulario->addSpan       ( $obSpnListaArqDigital           );
        $obFormulario->addComponente ( $obLblObservacao );
        $obFormulario->defineBarra(array($obBtnVoltar,$obBtnFiltro));;
    break;

}

$obFormulario->show();

processarForm(true,"Form",$stAcao);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
