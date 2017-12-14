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
    * Página de Formulário para fornecedor do módulo compras
    * Data de Criação   : 05/09/2006

    * @author Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso : uc-03.04.03

    $Id: FMManterFornecedor.php 60371 2014-10-15 23:06:27Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
include_once(CAM_GT_MON_COMPONENTES."IMontaAgenciaConta.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IMontaCatalogoClassificacao.class.php");
include_once(CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php");

$stPrograma = "ManterFornecedor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao   = $_REQUEST["stAcao"];
$inNumCgm = ($_REQUEST['inCGM'] ? $_REQUEST['inCGM'] : Sessao::read('inCGM') );

if ( Sessao::read('filtro') ) {
    $stFiltro = '';

    foreach ( Sessao::read('filtro') as $stCampo => $stValor ) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;
$inCodigo = $_REQUEST["inCodigo"];

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$stValorComposto = $_REQUEST["stValorComposto"];
$obHdnValorComposto = new Hidden;
$obHdnValorComposto->setName ( "stValorComposto" );
$obHdnValorComposto->setValue( $stValorComposto  );

$stCheked = $_REQUEST["stCheked"];
$obHdnChecked = new Hidden;
$obHdnChecked->setName( "stCheked" );
$obHdnChecked->setValue( $stCheked );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnChaveContaBancaria = new Hidden;
$obHdnChaveContaBancaria->setName( "stChaveConta" );
$obHdnChaveContaBancaria->setValue( '' );

Sessao::write('arContaBancaria' , array());
Sessao::write('arFornecedor' , array());
Sessao::write('arAtividades' , array());
Sessao::write('arSocio', array());

if ($stAcao == 'incluir') {
    //definição de componentes
    $obIpopUpCgm = new IPopUpCGM($obForm);
    $obIpopUpCgm->setRotulo("Fornecedor");
    $obIpopUpCgm->setTitle("Informe o fornecedor.");

    $obIpopUpCgm->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."'+'&'+this.name+'='+this.value,'buscaPisPasep');".$obIpopUpCgm->obCampoCod->obEvento->getOnChange() );
    $obIpopUpCgm->obCampoCod->obEvento->setOnFocus( "if (this.value != '') { ajaxJavaScript('".$pgOcul."?".Sessao::getId()."'+'&'+this.name+'='+this.value,'buscaPisPasep'); };");
}

if ($stAcao != 'incluir') {
    $obLblCGM = new Label();
    $obLblCGM->setRotulo('Fornecedor');
    $obLblCGM->setValue($_REQUEST["inCGM"]." - ".$_REQUEST["stNomCGM"]);

    $obHdnCGM = new Hidden;
    $obHdnCGM->setName( "inCGM" );
    $obHdnCGM->setValue( $_REQUEST["inCGM"] );

    $obHdnStatus = new Hidden;
    $obHdnStatus->setName( "stStatus" );
    $obHdnStatus->setValue( $_REQUEST["stStatus"] );
}

$tipoFornecedor = $_REQUEST['stTipo'];
if ($tipoFornecedor =='') {
    $tipoFornecedor = 'N';
}

if ( ($stAcao == 'incluir') or ($stAcao == 'alterar') ) {

    $obValor = new Moeda();
    $obValor->setRotulo("Valor Mínimo de Nota Fiscal");
    $obValor->setTitle("Informe o valor mínimo de nota fiscal.");
    $vl_minimo_nf = $_REQUEST["vl_minimo_nf"];
    if (!$vl_minimo_nf) {
        $vl_minimo_nf = '0,00';
    } else {
        $vl_minimo_nf = number_format( $vl_minimo_nf , 2, ",","." );
    }

    $obValor->setValue ( $vl_minimo_nf );

    $obRdbTipoNormal = new Radio;
    $obRdbTipoNormal->setTitle( "Informe o tipo de Fornecedor." );
    $obRdbTipoNormal->setName( "stTipoFornecedor" );
    $obRdbTipoNormal->setId( "stTipoFornecedor" );
    $obRdbTipoNormal->setChecked( $boAtivo);
    $obRdbTipoNormal->setValue( 'N' );
    $obRdbTipoNormal->setRotulo( "Tipo de Fornecedor" );
    $obRdbTipoNormal->setLabel( "Normal" );
    $obRdbTipoNormal->setNull( false );
    if ($tipoFornecedor == 'N') {
        $obRdbTipoNormal->setChecked( true );
    }

    $obRdbTipoMicroEmpresa = new Radio;
    $obRdbTipoMicroEmpresa->setName( "stTipoFornecedor" );
    $obRdbTipoMicroEmpresa->setId( "stTipoFornecedor" );
    $obRdbTipoMicroEmpresa->setValue( 'M' );
    $obRdbTipoMicroEmpresa->setLabel( "Microempresa" );
    if ($tipoFornecedor == 'M') {
        $obRdbTipoMicroEmpresa->setChecked( true );
    }

    $obRdbTipoPequenoPorte = new Radio;
    $obRdbTipoPequenoPorte->setName( "stTipoFornecedor" );
    $obRdbTipoPequenoPorte->setId( "stTipoFornecedor" );
    $obRdbTipoPequenoPorte->setValue( 'P' );
    $obRdbTipoPequenoPorte->setLabel( "Pequeno Porte" );
    if ($tipoFornecedor == 'P') {
        $obRdbTipoPequenoPorte->setChecked( true );
    }

}

$stStatus = $_REQUEST["stStatus"];
if ($stAcao != 'incluir') {
    $obLblStatus = new Label();
    $obLblStatus->setRotulo('Status');
    $obLblStatus->setValue($stStatus);
    if ($stAcao == 'alterar') {

        //monta listas do fornecedor
        include_once(CAM_GP_COM_MAPEAMENTO."TComprasFornecedorConta.class.php");
        $obTContaBancaria = new TComprasFornecedorConta();
        $obTContaBancaria->setDado('cgm_fornecedor',$_REQUEST["inCGM"]);
        $obTContaBancaria->recuperaListaFornecedorConta($rsContaBancaria);

        //Recuperando dados da conta bancária do fornecedor
          include_once(TMON."TMONBanco.class.php");
           $obTMONBanco = new TMONBanco() ;
           $obTMONBanco->recuperaTodos($rsBanco);
           $inCount = 0;
           while (!$rsBanco->eof()) {
               $arBanco[$rsBanco->getCampo('num_banco')]['cod_banco'] = $rsBanco->getCampo('cod_banco');
               $rsBanco->proximo();
           }
           include_once(TMON."TMONAgencia.class.php");
           $obTMONAgencia = new TMONAgencia() ;
           $obTMONAgencia->recuperaTodos($rsAgencia);
           while (!$rsAgencia->eof()) {
               $arBanco[$rsAgencia->getCampo('cod_banco')][$rsAgencia->getCampo('num_agencia')] = $rsAgencia->getCampo('cod_agencia');
               $rsAgencia->proximo();
           }

        $inCount = 0;
        while (!$rsContaBancaria->eof()) {
            $arContaBancaria[$inCount]['id_usuario' ] = $inCount;
            $arContaBancaria[$inCount]['banco'      ] = $rsContaBancaria->getCampo('num_banco');
            $arContaBancaria[$inCount]['agencia'    ] = $rsContaBancaria->getCampo('num_agencia');
            $arContaBancaria[$inCount]['conta'      ] = $rsContaBancaria->getCampo('num_conta');
            $arContaBancaria[$inCount]['padrao'     ] = $rsContaBancaria->getCampo('padrao') == t ? true : false;
            $arContaBancaria[$inCount]['cod_banco'  ] = $rsContaBancaria->getCampo('cod_banco');
            $arContaBancaria[$inCount]['cod_agencia'] = $rsContaBancaria->getCampo('cod_agencia');
            $arContaBancaria[$inCount]['nom_banco']   = $rsContaBancaria->getCampo('nom_banco');
            $arContaBancaria[$inCount]['nom_agencia'] = $rsContaBancaria->getCampo('nom_agencia' );

            $inCount++;
            $rsContaBancaria->proximo();
        }
        Sessao::write('arContaBancaria' , $arContaBancaria);
        
        
        include_once(CAM_GP_COM_MAPEAMENTO.'TComprasTipoSocio.class.php');
        include_once(CAM_GP_COM_MAPEAMENTO.'TComprasFornecedorSocio.class.php');
        include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");
        
        $obTComprasFornecedorSocio = new TComprasFornecedorSocio();
        $obTComprasFornecedorSocio->recuperaTodos($rsSocio, ' WHERE cgm_fornecedor='.$_REQUEST["inCGM"]);
        
        $inCount = 0;
        while (!$rsSocio->eof()) {                  
            $obTComprasTipoSocio = new TComprasTipoSocio();
            $obTComprasTipoSocio->setDado('cod_tipo',$rsSocio->getCampo("cod_tipo"));
            $obTComprasTipoSocio->recuperaPorChave($rsTipoSocio);
            
            $obTCGM = new TCGM;
            $obTCGM->setDado('numcgm', $rsSocio->getCampo("cgm_socio"));
            $obTCGM->recuperaPorChave($rsCGM);
            
            $arSocio[$inCount]['id']              = $rsSocio->getCampo('id');
            $arSocio[$inCount]['cod_tipo']        = $rsSocio->getCampo('cod_tipo');
            $arSocio[$inCount]['descricao']       = $rsTipoSocio->getCampo('descricao');
            $arSocio[$inCount]['numcgm']          = $rsSocio->getCampo('cgm_socio');
            $arSocio[$inCount]['nom_cgm']         = $rsCGM->getCampo('nom_cgm');
            $arSocio[$inCount]['ativo']           = $rsSocio->getCampo('ativo') ? 1:0;
            $arSocio[$inCount]['ativo_descricao'] = $rsSocio->getCampo('ativo') ? 'Sim':'Não';
            
            $rsSocio->proximo();
            $inCount++;
        }

        Sessao::write('arSocio' , $arSocio);        
        
        //Recuperando dados da classificação do fornecedor
        include_once(CAM_GP_COM_MAPEAMENTO."TComprasFornecedorClassificacao.class.php");
        $obTFornecedorClassificacao = new TComprasFornecedorClassificacao();
        $obTFornecedorClassificacao->setDado('cgm_fornecedor',$_REQUEST["inCGM"]);
        $obTFornecedorClassificacao->recuperaListaFornecedorClassificacao($rsFornecedor);
        $inCount = 0;
        while (!$rsFornecedor->eof()) {
            $arFornecedor[$inCount]['cgm_fornecedor'   ] = $rsFornecedor->getCampo('cgm_fornecedor');
            $arFornecedor[$inCount]['catalogo'         ] = $rsFornecedor->getCampo('catalogo');
            $arFornecedor[$inCount]['cod_catalogo'     ] = $rsFornecedor->getCampo('cod_catalogo');
            $arFornecedor[$inCount]['classificacao'    ] = $rsFornecedor->getCampo('classificacao');
            $arFornecedor[$inCount]['cod_classificacao'] = $rsFornecedor->getCampo('cod_classificacao');
            $arFornecedor[$inCount]['descricao'        ] = $rsFornecedor->getCampo('descricao');
            $inCount++;
            $rsFornecedor->proximo();
        }
        Sessao::write('arFornecedor' , $arFornecedor);

        //Recuperando dados das atividades do fornecedor
        include_once(CAM_GP_COM_MAPEAMENTO."TComprasFornecedorAtividade.class.php");
        $obTFornecedorAtividades = new TComprasFornecedorAtividade();
        $inCGM = $_REQUEST["inCGM"];
        $obTFornecedorAtividades->setDado('cgm_fornecedor',$inCGM);
        $obTFornecedorAtividades->recuperaAtividadeFornecedor($rsFornecedorAtividades);
        $inCount = 0;
        while (!$rsFornecedorAtividades->eof()) {
            $arAtividades[$inCount]['cgm_fornecedor'] = $rsFornecedorAtividades->getCampo('cgm_fornecedor');
            $arAtividades[$inCount]['cod_atividade' ] = $rsFornecedorAtividades->getCampo('cod_atividade');
            $arAtividades[$inCount]['atividade'     ] = $rsFornecedorAtividades->getCampo('nom_atividade');
            $arAtividades[$inCount]['codigo'        ] = $rsFornecedorAtividades->getCampo('cod_estrutural');
            $inCount++;
            $rsFornecedorAtividades->proximo();
        }
        Sessao::write('arAtividades' , $arAtividades);
        Sessao::write('inCGM' , $inCGM);
        $jsOnLoad = "executaFuncaoAjax('montaRecuperaFormulario')";
    }
}
//componentes para lista de inclusão de conta banco (DADOS BANCARIOS)
$obIMontaAgenciaConta = new IMontaAgenciaConta();
// TEXT COMBO - BANCO
$obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->setNull(true);
$obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->setObrigatorioBarra(true);
// TEXT COMBO - AGENCIA
$obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->setNull(true);
$obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->setObrigatorioBarra(true);
// BUSCA INNER - CONTA CORRENTE
$obIMontaAgenciaConta->obBscConta->setNull(true);
$obIMontaAgenciaConta->obBscConta->setObrigatorioBarra(true);

//valida banco
$obITextBoxBanco   = & $obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->obTextBox;
$obISelectBanco    = & $obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->obSelect;

//valida agencia
$obITextBoxAgencia = & $obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->obTextBox;
$obSelectAgencia   = & $obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->obSelect;

//valida conta
$obSelectAgenciaConta = $obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia;

$arIncluirContaBanco = array ( $obITextBoxBanco,$obISelectBanco, $obITextBoxAgencia,$obSelectAgencia, $obSelectAgenciaConta );

// Define objeto span para lista de conta bancaria
$obSpnListaContaBancaria = new Span();
$obSpnListaContaBancaria->setId( "spnListaContaBancaria" );

//componentes de inclusão de catálogo fornecedor
//define objeto do componente imontacatalogoclassificacao
$obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao;
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNull(true);

// seta para que apenas venha catalogos com classificação
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setApenasComClassificacao(true);

$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNaoPermiteManutencao(true);
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->obTextBox->setObrigatorioBarra(true);
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->obTextBox->setNull(true);
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->obSelect->setObrigatorioBarra(true);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido(false);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida(false);

$obITextBoxSelectCatalogo = $obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->obTextBox;
$obSelectCatalogo = $obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->obSelect;
//$obClassificacao  = $obIMontaCatalogoClassificacao->obIMontaClassificacao->obCmbClassificacao;

$arCatalogoClassificacao = array($obITextBoxSelectCatalogo,$obSelectCatalogo, $obSpnListaContaBancaria );

// Define objeto span para lista de catalogo fornecedor
$obSpnListaFornecedor = new Span();
$obSpnListaFornecedor->setId( "spnListaFornecedor" );

$obBscAtividade = new BuscaInner;
$obBscAtividade->setRotulo              ( "Atividade"                     );
$obBscAtividade->setTitle               ( "Atividade Econômica"           );
$obBscAtividade->setId                  ( "campoInner"                    );
$obBscAtividade->setNull                ( true                            );
$obBscAtividade->setObrigatorioBarra    ( true                            );
$obBscAtividade->obCampoCod->setName    ( "inCodigoAtividade"             );
$obBscAtividade->obCampoCod->setInteiro ( false                           );

$obBscAtividade->obCampoCod->obEvento->setOnChange ( "montaParametrosGET( 'preencheInnerAtividade', 'inCodigoAtividade' );"          );
$obBscAtividade->obCampoCod->obEvento->setOnBlur   ( "montaParametrosGET( 'buscaAtividade', 'inCodigoAtividade, stValorComposto' );" );
$stBusca  = "abrePopUp('".CAM_GT_CEM_POPUPS."atividadeeconomica/FLProcurarAtividade.php','frm','stValorComposto','campoInner',''";
$stBusca .= " ,'".Sessao::getId()."&campoNom=campoInner&campoNum=stValorComposto&campoFoco=inCodigoAtividade','800','550')";
$obBscAtividade->setFuncaoBusca        ( $stBusca                         );

//Define Objeto Button para Icluir Atividade
$obBtnIncluirAtividade = new Button;
$obBtnIncluirAtividade->setValue( "Incluir" );
$obBtnIncluirAtividade->obEvento->setOnClick( "buscaValor('incluirAtividade');" );

//Define Objeto Button para limpar Atividade
$obBtnLimparAtividade = new Button;
$obBtnLimparAtividade->setValue( "Limpar" );
$obBtnLimparAtividade->obEvento->setOnClick( "buscaValor('limparAtividade');" );

// Span com detalhamento das atividades
$obSpnDetalheAtividade = new Span();
$obSpnDetalheAtividade->setId( "spnDetalheAtividade" );

$arIncluiAtividades = array ();
$arIncluiAtividades[] = $obBscAtividade;
$arIncluiAtividades[] = $obSpnDetalheAtividade;

// Define objeto span para lista de atividade
$obSpnListaAtividade = new Span();
$obSpnListaAtividade->setId( "spnListaAtividade" );

if ($stAcao == 'ativar/desativar') {
    if ($stStatus == 'Ativo') {
        $obTextAreaMotivo = new TextArea();
        $obTextAreaMotivo->setRotulo('Motivo');
        $obTextAreaMotivo->setName('stMotivo');
        $obTextAreaMotivo->setNull(false);
        $obTextAreaMotivo->setMaxCaracteres(200);
    } else {
        $obTextAreaMotivo = new Label();
        $obTextAreaMotivo->setRotulo('Motivo');
        $obTextAreaMotivo->setName('stMotivo');
        $obTextAreaMotivo->setValue($stMotivo);
    }
}

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                  );
$obFormulario->setAjuda         ( "UC-03.04.03"            );
$obFormulario->addHidden        ( $obHdnCtrl               );
$obFormulario->addHidden        ( $obHdnChecked            );
$obFormulario->addHidden        ( $obHdnAcao               );
$obFormulario->addHidden        ( $obHdnValorComposto      );
$obFormulario->addHidden        ( $obHdnChaveContaBancaria );
$obFormulario->addTitulo        ( "Dados do Fornecedor"    );

if ($stAcao == 'incluir')
    $obFormulario->addComponente($obIpopUpCgm);
if ($stAcao != 'incluir') {
    $obFormulario->addHidden($obHdnCGM);
    $obFormulario->addHidden($obHdnStatus);
    $obFormulario->addComponente($obLblCGM);
}

$obSpnPisPasep = new Span;
$obSpnPisPasep->setId ('spnPisPasep');
$obFormulario->addSpan($obSpnPisPasep);

if ( ($stAcao == 'incluir' ) or ( $stAcao == 'alterar' ) ) {
    $obFormulario->addComponente($obValor);
    $obFormulario->agrupaComponentes( array( $obRdbTipoNormal, $obRdbTipoMicroEmpresa, $obRdbTipoPequenoPorte ) );
}
if ($stAcao != 'incluir') {
    $obFormulario->addComponente($obLblStatus);
}
if ($stAcao != 'ativar/desativar') {
    $obFormulario->addTitulo        ( "Dados Bancários"   );
    $obIMontaAgenciaConta->geraFormulario( $obFormulario );
    $obFormulario->IncluirAlterar ( 'ContaBancaria', $arIncluirContaBanco , true);
    $obFormulario->addSpan($obSpnListaContaBancaria);

########################    
    include_once ( CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php' );
    include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasTipoSocio.class.php' );
    include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasFornecedorSocio.class.php' );

    $obFormulario->addTitulo( "Sócios" );

    $obCGMSocio = new IPopUpCGM($obForm);
    $obCGMSocio->setTipo  ("fisica");
    $obCGMSocio->setRotulo( "CGM do Sócio"          );
    $obCGMSocio->setTitle ( "Informe o CGM do Sócio." );
    $obCGMSocio->setName            ( 'stNomSocio' );
    $obCGMSocio->setId              ( 'stNomSocio' );
    $obCGMSocio->obCampoCod->setName( 'cgmSocio'   );
    $obCGMSocio->obCampoCod->setId  ( 'cgmSocio'   );
    $obFormulario->addComponente( $obCGMSocio      );
    
    $rsTipo = new Recordset;
    $obTComprasTipoSocio = new TComprasTipoSocio;
    $obTComprasTipoSocio->recuperaTodos($rsTipo);
    
    $obCmbTipo = new Select;
    $obCmbTipo->setTitle       ( "Selecione o Tipo" );
    $obCmbTipo->setName        ( "inCodTipo"        );
    $obCmbTipo->setId          ( "inCodTipo"        );
    $obCmbTipo->setRotulo      ( "Tipo"             );
    $obCmbTipo->addOption      ( '', 'Selecione'    );
    $obCmbTipo->setCampoId     ( "cod_tipo"         );
    $obCmbTipo->setCampoDesc   ( "descricao"        );
    $obCmbTipo->preencheCombo  ( $rsTipo            );
    $obFormulario->addComponente( $obCmbTipo        );
    
    $obRdbAtivoSim = new Radio;
    $obRdbAtivoSim->setRotulo( "Ativo" );
    $obRdbAtivoSim->setName( "boAtivo" );
    $obRdbAtivoSim->setId( "boAtivo" );
    $obRdbAtivoSim->setValue( '1' );
    $obRdbAtivoSim->setLabel( "Sim" );
    $obRdbAtivoSim->setChecked( true );
    
    $obRdbAtivoNao = new Radio;
    $obRdbAtivoNao->setRotulo( "Ativo" );
    $obRdbAtivoNao->setName( "boAtivo" );
    $obRdbAtivoNao->setId( "boAtivo" );
    $obRdbAtivoNao->setValue( '0' );
    $obRdbAtivoNao->setLabel( "Não" );
    $obRdbAtivoNao->setChecked( false );
    
    $obFormulario->agrupaComponentes(array($obRdbAtivoSim, $obRdbAtivoNao));
    
    $arSocio = array ();
    $arSocio[] = $obCGMSocio;
    $arSocio[] = $obCmbTipo;
    $arSocio[] = $obRdbAtivoSim;
    $arSocio[] = $obRdbAtivoNao;
    
    $obFormulario->Incluir('Socio', $arSocio, true, false, 'cgmSocio, inCodTipo, boAtivo');
    
    // Define objeto span para lista de atividade
    $obSpnListaSocio = new Span();
    $obSpnListaSocio->setId( "spnListaSocio" );
    $obFormulario->addSpan($obSpnListaSocio);
########################    
    
    
    $obFormulario->addTitulo        ( "Dados da Classificação"   );
    $obIMontaCatalogoClassificacao->geraFormulario($obFormulario);
    $obFormulario->Incluir ('Fornecedor', $arCatalogoClassificacao,true);
    $obFormulario->addSpan($obSpnListaFornecedor);

    $obFormulario->addTitulo        ( "Ramos de Atividade"   );

    $obFormulario->addComponente( $obBscAtividade );

    $obFormulario->addSpan($obSpnDetalheAtividade);
    $obFormulario->incluir( 'Atividade' , $arIncluiAtividades , true );

    $obFormulario->addSpan($obSpnListaAtividade);
} else {
    $obFormulario->addTitulo        ( "Dados da Ativação/Desativação"   );
    $obFormulario->addComponente($obTextAreaMotivo);
}

if ($stAcao == 'incluir') {
    $obFormulario->OK();
} else {
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obFormulario->Cancelar( $stLocation );

}
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
include ($pgJs);
