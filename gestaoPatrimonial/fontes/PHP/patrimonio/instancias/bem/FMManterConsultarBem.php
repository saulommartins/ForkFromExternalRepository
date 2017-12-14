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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-03.01.06

    $Id: FMManterConsultarBem.php 63945 2015-11-10 18:53:13Z arthur $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemAtributoEspecie.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_FW_HTML."MontaAtributos.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemProcesso.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioReavaliacao.class.php";

$stPrograma = "ManterConsultarBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LSManterBem.php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include_once($pgJs);

$stAcao = $request->get('stAcao');

//recupera os dados do bem
$obTPatrimonioBem = new TPatrimonioBem();
$obTPatrimonioBem->setDado( 'cod_bem', $request->get('inCodBem') );
$obTPatrimonioBem->recuperaRelacionamentoAnalitico( $rsBem );
$obTPatrimonioBem->recuperaValorDepreciacao( $rsDepreciacao );

$obTPatrimonioReavaliacao = new TPatrimonioReavaliacao();
$obTPatrimonioReavaliacao->setDado( 'cod_bem', $request->get('inCodBem') );
$obTPatrimonioReavaliacao->recuperaUltimaReavaliacao ( $rsUltimaReavaliacao );

$obTPatrimonioBemProcesso = new TPatrimonioBemProcesso();
$obTPatrimonioBemProcesso->setDado( 'cod_bem', $request->get('inCodBem') );
$obTPatrimonioBemProcesso->recuperaPorChave( $rsBemProcesso );

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgList);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue('');

//label para o codigo do bem
$obLblCodBem = new Label();
$obLblCodBem->setRotulo( 'Código do Bem' );
$obLblCodBem->setValue( $rsBem->getCampo( 'cod_bem' ) );

//label para a natureza
$obLblNatureza = new Label();
$obLblNatureza->setRotulo( 'Natureza' );
$obLblNatureza->setValue( $rsBem->getCampo( 'cod_natureza' ).' - '.$rsBem->getCampo( 'nom_natureza' ) );

//label para o grupo
$obLblGrupo = new Label();
$obLblGrupo->setRotulo( 'Grupo' );
$obLblGrupo->setValue( $rsBem->getCampo( 'cod_grupo' ).' - '.$rsBem->getCampo( 'nom_grupo' ) );

//label para a especie
$obLblEspecie = new Label();
$obLblEspecie->setRotulo( 'Espécie' );
$obLblEspecie->setValue( $rsBem->getCampo( 'cod_especie' ).' - '.$rsBem->getCampo( 'nom_especie' ) );

//label para a descricao do bem
$obLblDescricaoBem = new Label();
$obLblDescricaoBem->setRotulo( 'Descrição' );
$obLblDescricaoBem->setValue( $rsBem->getCampo( 'descricao' ) );

//label para o detalhamento do processo administrativo
$obProcessoLicitatorio = new Label();
$obProcessoLicitatorio->setId( 'stProcesso' );
$obProcessoLicitatorio->setValue( str_pad($rsBemProcesso->getCampo('cod_processo')."/".$rsBemProcesso->getCampo('ano_exercicio'),10,'0',STR_PAD_LEFT));
$obProcessoLicitatorio->setRotulo( 'Processo Administrativo' );

//label para a detalhamento do bem
$obLblDetalhamentoBem = new Label();
$obLblDetalhamentoBem->setRotulo( 'Detalhamento' );
$obLblDetalhamentoBem->setValue( $rsBem->getCampo( 'detalhamento' ) );

//label para o fornecedor
$obLblFornecedor = new Label();
$obLblFornecedor->setRotulo( 'Fornecedor' );
$obLblFornecedor->setValue( ($rsBem->getCampo('num_fornecedor') != '' ) ? $rsBem->getCampo( 'num_fornecedor').' - '.$rsBem->getCampo( 'nom_fornecedor' ) : '' );

//label para valor do bem
$obLblValorBem = new Label();
$obLblValorBem->setRotulo( 'Valor do Bem' );
$obLblValorBem->setValue( number_format($rsBem->getCampo('vl_bem'),2,',','.') );

//label para o valor da depreciacao Inicial
$obLblValorDepreciacaoInicial = new Label();
$obLblValorDepreciacaoInicial->setRotulo( 'Valor da Depreciação Inicial' );
$obLblValorDepreciacaoInicial->setValue( $rsBem->getCampo( 'vl_depreciacao' ) != '' ? number_format($rsBem->getCampo( 'vl_depreciacao' ),2,',','.') : '0,00' );

//label para o valor da depreciacao Acumulada
$obLblDepreciacaoAcumuladaExercicio = new Label();
$obLblDepreciacaoAcumuladaExercicio->setRotulo( 'Depreciação Acumulada' );
$obLblDepreciacaoAcumuladaExercicio->setValue( $rsDepreciacao->getCampo('vl_acumulado') != '' ? number_format($rsDepreciacao->getCampo('vl_acumulado'),2,',','.') : '0,00');

//label para a data da depreciação
$obLblDataDepreciacao = new Label();
$obLblDataDepreciacao->setRotulo( 'Data da Depreciação' );
$obLblDataDepreciacao->setValue( $rsDepreciacao->getCampo('dt_depreciacao') );

//label para a data da aquisicao
$obLblDataAquisicao = new Label();
$obLblDataAquisicao->setRotulo( 'Data da Aquisição' );
$obLblDataAquisicao->setValue( $rsBem->getCampo('dt_aquisicao') );

//label para a data de incorporação
$obLblDataIncorporacao = new Label();
$obLblDataIncorporacao->setRotulo( 'Data de Incorporação' );
$obLblDataIncorporacao->setValue( $rsBem->getCampo('dt_incorporacao') );

//label para o vencimento da garantia
$obLblVencimentoGarantia = new Label();
$obLblVencimentoGarantia->setRotulo( 'Vencimento da Garantia' );
$obLblVencimentoGarantia->setValue( $rsBem->getCampo('dt_garantia') );

//label para o numero da placa
$obLblNumeroPlaca = new Label();
$obLblNumeroPlaca->setRotulo( 'Número da Placa' );
$obLblNumeroPlaca->setValue( $rsBem->getCampo('num_placa') );

//recupera os atributos do item
$obRCadastroDinamico = new RCadastroDinamico();
$obRCadastroDinamico->setCodCadastro( 1 );
$obRCadastroDinamico->obRModulo->setCodModulo( 6 );
$obRCadastroDinamico->setChavePersistenteValores( array( 'cod_bem' => $_REQUEST['inCodBem'], 'cod_especie' => $rsBem->getCampo('cod_especie'), 'cod_grupo' => $rsBem->getCampo('cod_grupo') ,'cod_natureza' => $rsBem->getCampo('cod_natureza') ) );
$obRCadastroDinamico->setPersistenteAtributos( new TPatrimonioEspecieAtributo );
$obRCadastroDinamico->setPersistenteValores( new TPatrimonioBemAtributoEspecie );
$obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

// Atributos Dinamicos
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
$obMontaAtributos->setLabel      ( true );

//label para a entidade
$obLblEntidade = new Label();
$obLblEntidade->setRotulo( 'Entidade' );
$obLblEntidade->setValue( $rsBem->getCampo('cod_entidade').' - '.$rsBem->getCampo('nom_entidade') );

// Label para a Orgao
$obLblOrgaoA = new Label();
$obLblOrgaoA->setRotulo   ("Órgão");
if ($rsBem->getCampo('num_orgao_a') != ''){
    $obLblOrgaoA->setValue    ($rsBem->getCampo('num_orgao_a').' - '.$rsBem->getCampo('nom_orgao_a') );
}

// LAbel para Unidade
$obLblUnidadeA = new Label();
$obLblUnidadeA->setRotulo   ("Unidade");
if ($rsBem->getCampo('num_unidade_a') != ''){
    $obLblUnidadeA->setValue    ($rsBem->getCampo('num_unidade_a').' - '.$rsBem->getCampo('nom_unidade_a') );
}
//label para o exercicio
$obLblExercicio = new Label();
$obLblExercicio->setRotulo( 'Exercício' );
$obLblExercicio->setValue( $rsBem->getCampo('exercicio') );

//label para o empenho
$obLblEmpenho = new Label();
$obLblEmpenho->setRotulo( 'Número do Empenho' );
$obLblEmpenho->setValue( $rsBem->getCampo('cod_empenho') );

//label para o numero da nota fiscal
$obLblNumNotaFiscal = new Label();
$obLblNumNotaFiscal->setRotulo( 'Número da Nota Fiscal' );
$obLblNumNotaFiscal->setValue( $rsBem->getCampo('nota_fiscal') );

$obLocalizacao = new Link;
$obLocalizacao->setRotulo("Download da Nota Fiscal");
$obLocalizacao->setHref( CAM_GP_PAT_ANEXOS.$rsBem->getCampo( 'caminho_nf' ));
if($rsBem->getCampo( 'caminho_nf' ) != '') {
    $obLocalizacao->setValue ($rsBem->getCampo( 'caminho_nf' ));
}
$obLocalizacao->setTarget("oculto");

//label para o responsavel
$obLblResponsavel = new Label();
$obLblResponsavel->setRotulo( 'Responsável' );
$obLblResponsavel->setValue( ( $rsBem->getCampo('num_responsavel') != '' ) ? $rsBem->getCampo('num_responsavel').' - '.$rsBem->getCampo('nom_responsavel') : '' );

//label para a data de inicio do responsavel
$obLblDtInicioResponsavel = new Label();
$obLblDtInicioResponsavel->setRotulo( 'Data de Início' );
$obLblDtInicioResponsavel->setValue( $rsBem->getCampo('dt_inicio') );

//pega a mascara para o localizacao
$arMascara = explode('.',sistemaLegado::pegaConfiguracao( 'mascara_local',2 ));
$arMascara[4] = explode('/',$arMascara[4]);

//label para o orgao
$obLblOrgao = new Label();
$obLblOrgao->setRotulo( 'Localização' );
$obLblOrgao->setValue( $rsBem->getCampo('orgao_resumido').' - '.$rsBem->getCampo('nom_orgao') );

//label para a Local
$obLblLocal = new Label();
$obLblLocal->setRotulo( 'Local' );
$obLblLocal->setValue( $rsBem->getCampo('cod_local').' - '.$rsBem->getCampo('nom_local') );

//label para a seguradora
$obLblSeguradora = new Label();
$obLblSeguradora->setRotulo( 'Seguradora' );
$obLblSeguradora->setValue( $rsBem->getCampo('num_seguradora').' - '.$rsBem->getCampo('nom_seguradora') );

//label para a apolice
$obLblApolice = new Label();
$obLblApolice->setRotulo( 'Apólice' );
$obLblApolice->setValue( $rsBem->getCampo('num_apolice') );

//label para o vencimento ta apolice
$obLblVencimentoApolice = new Label();
$obLblVencimentoApolice->setRotulo( 'Vencimento' );
$obLblVencimentoApolice->setValue( $rsBem->getCampo('vencimento_apolice') );

//label para a data de baixa
$obLblDtBaixa = new Label();
$obLblDtBaixa->setRotulo( 'Data da Baixa' );
$obLblDtBaixa->setValue( $rsBem->getCampo('dt_baixa') );

//label para o motivo da baixa
$obLblMotivoBaixa = new Label();
$obLblMotivoBaixa->setRotulo( 'Motivo' );
$obLblMotivoBaixa->setValue( $rsBem->getCampo('motivo') );

$obLblDataUltimaReavaliacao = new Label();
$obLblDataUltimaReavaliacao->setRotulo ( 'Data Última Reavaliação' );
$obLblDataUltimaReavaliacao->setValue  ( $rsUltimaReavaliacao->getCampo('dt_reavaliacao') );

$obLblValorltimaReavaliacao = new Label();
$obLblValorltimaReavaliacao->setRotulo ( 'Valor Última Reavaliação' );
$obLblValorltimaReavaliacao->setValue  ( $rsUltimaReavaliacao->getCampo('vl_reavaliacao') != '' ? number_format($rsUltimaReavaliacao->getCampo('vl_reavaliacao'),2,',','.') : '0,00');

//cria um button para a acao voltar
$obBtnVoltar = new Button;
$obBtnVoltar->setName              ( "btnVoltar" );
$obBtnVoltar->setValue             ( "Voltar" );
$obBtnVoltar->setTipo              ( "button" );
$obBtnVoltar->obEvento->setOnClick ( "document.location = '".$pgList."?".Sessao::getId()."&stAcao=consultar';" );
$obBtnVoltar->setDisabled          ( false );

switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio())) {
    case 02:
        include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALBemCompradoTipoDocumentoFiscal.class.php';
        $obTTCEALBemCompradoTipoDocumentoFiscal = new TTCEALBemCompradoTipoDocumentoFiscal;
        $obTTCEALBemCompradoTipoDocumentoFiscal->setDado( "cod_bem", $rsBem->getCampo('cod_bem') );
        $obTTCEALBemCompradoTipoDocumentoFiscal->recuperaPorChave($rsDocumento,$boTransacao);
        
        if ($rsDocumento->getNumLinhas() > 0) {
            include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALTipoDocumentoFiscal.class.php';
            $obTTCEALTipoDocumentoFiscal = new TTCEALTipoDocumentoFiscal;
            $obTTCEALTipoDocumentoFiscal->setDado("cod_tipo_documento_fiscal", $rsDocumento->getCampo( "cod_tipo_documento_fiscal" ) );
            $obTTCEALTipoDocumentoFiscal->recuperaPorChave($rsTipoDocumento, $boTransacao);
            
            $stDescricaoDocumentoFiscal=$rsTipoDocumento->getCampo( "descricao"                 );
            $inTipoDocumentoFiscal=$rsDocumento->getCampo         ( "cod_tipo_documento_fiscal" );
        }
    break;
}

$obLblTipoDocumentoFiscal = new Label;
$obLblTipoDocumentoFiscal->setRotulo ( "Tipo do Documento Fiscal" );
$obLblTipoDocumentoFiscal->setId     ( "stTpDocumentoFiscal"      );
$obLblTipoDocumentoFiscal->setValue  ( !empty($inTipoDocumentoFiscal) ? $inTipoDocumentoFiscal." - " .$stDescricaoDocumentoFiscal : "" );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.01.06');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Dados do Bem' );
$obFormulario->addComponente( $obLblCodBem );

$obFormulario->addTitulo    ( 'Classificação' );
$obFormulario->addComponente( $obLblNatureza );
$obFormulario->addComponente( $obLblGrupo );
$obFormulario->addComponente( $obLblEspecie );

$obFormulario->addTitulo    ( 'Informações Básicas' );
$obFormulario->addComponente( $obLblDescricaoBem );
$obFormulario->addComponente( $obProcessoLicitatorio );
$obFormulario->addComponente( $obLblDetalhamentoBem );
$obFormulario->addComponente( $obLblFornecedor );
$obFormulario->addComponente( $obLblValorBem );
$obFormulario->addComponente( $obLblDataAquisicao );
$obFormulario->addComponente( $obLblVencimentoGarantia );
$obFormulario->addComponente( $obLblNumeroPlaca );

$obMontaAtributos->geraFormulario( $obFormulario );

$obFormulario->addTitulo    ( 'Informações Financeiras' );
$obFormulario->addComponente( $obLblExercicio );
$obFormulario->addComponente( $obLblEntidade );
$obFormulario->addComponente( $obLblOrgaoA );
$obFormulario->addComponente( $obLblUnidadeA );
$obFormulario->addComponente( $obLblDataIncorporacao );
$obFormulario->addComponente( $obLblEmpenho );
$obFormulario->addComponente( $obLblNumNotaFiscal );
$obFormulario->addComponente( $obLocalizacao );

switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio())) {
    case 02:
        $obFormulario->addComponente($obLblTipoDocumentoFiscal      );
    break;
}

$obFormulario->addTitulo     ( 'Depreciação Inicial / Última Reavaliação' );
$obFormulario->addComponente ( $obLblValorDepreciacaoInicial );
$obFormulario->addComponente ( $obLblDepreciacaoAcumuladaExercicio );
$obFormulario->addComponente ( $obLblDataDepreciacao );
$obFormulario->addComponente ( $obLblDataUltimaReavaliacao );
$obFormulario->addComponente ( $obLblValorltimaReavaliacao );

$obFormulario->addTitulo	( 'Responsável' );
$obFormulario->addComponente( $obLblResponsavel );
$obFormulario->addComponente( $obLblDtInicioResponsavel );

$obFormulario->addTitulo  	( 'Histórico' );
$obFormulario->addComponente( $obLblOrgao );
$obFormulario->addComponente( $obLblLocal );

$obFormulario->addTitulo    ( 'Apólice' );
$obFormulario->addComponente( $obLblSeguradora );
$obFormulario->addComponente( $obLblApolice );
$obFormulario->addComponente( $obLblVencimentoApolice );

if ( $rsBem->getCampo('dt_baixa') != '' ) {
    $obFormulario->addTitulo( 'Baixa' );
    $obFormulario->addComponente( $obLblDtBaixa );
    $obFormulario->addComponente( $obLblMotivoBaixa );
}

$obFormulario->defineBarra( array($obBtnVoltar), 'left', '' );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
