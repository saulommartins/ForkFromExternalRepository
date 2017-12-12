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
    * Página de Formulário Configuração Exportação TCM/BA
    * Data de Criação: 20/12/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Finger

    * @ignore

    * Casos de uso: uc-04.08.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_IMA_COMPONENTES."ISelectMultiploRegime.class.php"                                );
include_once ( CAM_GRH_IMA_COMPONENTES."ISelectMultiploSubDivisao.class.php"                            );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAExportacaoTCMBA.class.php"                                   );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAExportacaoTCMBASubDivisao.class.php"                         );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMATipoServidor.class.php"                                      );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"                                    );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php"                                        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTCMBA";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgDown     = "DW".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stAcao = ($_REQUEST['stAcao'] != '') ?  $_REQUEST['stAcao'] : 'configurar';

function preencherSubDivisaoOnload($arDados, $stServidor)
{
    $stJs = '';
    if ( is_array($arDados) ) {
        foreach ($arDados as $inCodRegime) {
            $stCodSubDivisao .= $inCodRegime.',';
        }
        $stCodSubDivisao = substr($stCodSubDivisao, 0, strlen($stCodSubDivisao)-1);

        //CARREGA AS SUBDIVISÔES
        $obTPessoalSubDivisao = new TPessoalSubDivisao;
        $stFiltro = ' AND psd.cod_sub_divisao IN ('.$stCodSubDivisao.')';
        $obTPessoalSubDivisao->recuperaRelacionamento( $rsPessoalSubDivisao , $stFiltro, "", $boTransacao );

        $inIndex = 0;
        while ( !$rsPessoalSubDivisao->eof() ) {
            $stJs .= 'for (i = 0; i < document.frm.inCodRegimeDisponiveis'.$stServidor.".options.length; i++) { \n";
            $stJs .= '  if ( document.frm.inCodRegimeDisponiveis'.$stServidor.'.options[i].value == \''.$rsPessoalSubDivisao->getCampo("cod_regime")."') {\n";
            $stJs .= '      document.frm.inCodRegimeDisponiveis'.$stServidor.".options[i] = null;\n";
            $stJs .= "  }\n";
            $stJs .= "}\n";
            $stJs .= 'document.frm.inCodSubDivisaoSelecionados'.$stServidor.'['.$inIndex.'] = new Option(\''.$rsPessoalSubDivisao->getCampo("nom_sub_divisao").'\','.$rsPessoalSubDivisao->getCampo("cod_sub_divisao").", '');\n";
            $stCodRegime .= ( strstr($stCodRegime, $rsPessoalSubDivisao->getCampo("cod_regime")) ) ? '' : $rsPessoalSubDivisao->getCampo("cod_regime").',';

            $arCodSubDivisao[] = $rsPessoalSubDivisao->getCampo("cod_sub_divisao");
            $inIndex++;
            $rsPessoalSubDivisao->proximo();
        }
        $stCodRegime = substr($stCodRegime, 0, strlen($stCodRegime)-1);

        $stFiltro = ' AND psd.cod_regime IN ('.$stCodRegime.')';
        $obTPessoalSubDivisao->recuperaRelacionamento( $rsSubDivisao , $stFiltro, '', $boTransacao );

        $inIndex = 0;
        while ( !$rsSubDivisao->eof() ) {
            if ( !in_array($rsSubDivisao->getCampo("cod_sub_divisao"), $arCodSubDivisao) ) {
                $stJs .= 'document.frm.inCodSubDivisaoDisponiveis'.$stServidor.'['.$inIndex."] = new Option('".$rsSubDivisao->getCampo("nom_sub_divisao")."', '".$rsSubDivisao->getCampo("cod_sub_divisao")."' ,'');\n";
                $inIndex++;
            }
            $rsSubDivisao->proximo();
        }

        //CARREGA OS REGIMES
        $obTPessoalRegime = new TPessoalRegime;
        $stFiltro = ' WHERE  cod_regime IN ('.$stCodRegime.')';
        $obTPessoalRegime->recuperaTodos( $rsPessoalRegime , $stFiltro, "", $boTransacao );

        $inIndex = 0;
        while ( !$rsPessoalRegime->eof() ) {
            $stJs .= 'document.frm.inCodRegimeSelecionados'.$stServidor.'['.$inIndex.'] = new Option(\''.$rsPessoalRegime->getCampo("descricao").'\','.$rsPessoalRegime->getCampo("cod_regime").", '');\n";
            $inIndex++;
            $rsPessoalRegime->proximo();
        }
    }

    return $stJs;
}

////############################################################################
////                      Recebe os dados do banco                             #
////############################################################################
$obTIMAExportacaoTCMBA = new TIMAExportacaoTCMBA;
$obTIMAExportacaoTCMBA->recuperaTodos($rsExportacaoTCMBA);

$rsExportacaoTCMBA->setPrimeiroElemento();
$inCodEntidade = $rsExportacaoTCMBA->getCampo("cod_entidade");
$inNumEntidade = $rsExportacaoTCMBA->getCampo("num_entidade");
//$rsExportacaoTCMBA->getCampo("cod_configuracao");

$obTIMAExportacaoTCMBASubDivisao = new TIMAExportacaoTCMBASubDivisao;
$obTIMAExportacaoTCMBASubDivisao->recuperaTodos( $rsExportacaoTCMBASubDivisao );

$jsOnload = '';
while ( !$rsExportacaoTCMBASubDivisao->eof() ) {

    switch ($rsExportacaoTCMBASubDivisao->getCampo("cod_tipo_servidor")) {
        case 1:
            $arEfetivo[] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
        break;
       case 2:
           $arCeletista[] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
        break;
        case 3:
            $arCargoComissao[] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
        break;
        case 5:
            $arTrabalhadorTemporario[] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
        break;
        case 6:
            $arAgentePolitico[] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
        break;
    }
    $rsExportacaoTCMBASubDivisao->proximo();
}
if (is_array($arEfetivo)) {
    $jsOnload .= preencherSubDivisaoOnload( $arEfetivo, 1);
}
if (is_array($arCeletista)) {
    $jsOnload .= preencherSubDivisaoOnload( $arCeletista, 2);
}
if (is_array($arCargoComissao)) {
    $jsOnload .= preencherSubDivisaoOnload( $arCargoComissao, 3);
}
if (is_array($arTrabalhadorTemporario)) {
    $jsOnload .= preencherSubDivisaoOnload( $arTrabalhadorTemporario, 5);
}
if (is_array($arAgentePolitico)) {
    $jsOnload .= preencherSubDivisaoOnload( $arAgentePolitico, 6);
}

//############################################################################
//                      Final da transação                                   #
//############################################################################

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

//Definicao dos componentes
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setId                               ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setId                               ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( ""                                                                    );

//Entidade
$obCmbEntidade = new Select;
$obCmbEntidade->setRotulo       ( "Código da Entidade"                                                                  );
$obCmbEntidade->setName         ( "inCodEntidade"                                                                       );
$obCmbEntidade->setStyle        ( "width: 200px"                                                                        );
$obCmbEntidade->setTitle        ( "Selecione o código da Entidade: Prefeitura, Câmara ou Descentralizada."              );
$obCmbEntidade->setId           ( "inCodEntidade"                                                                       );
$obCmbEntidade->addOption       ( "", "Selecione"                                                                       );
$obCmbEntidade->addOption       ( "P", "P - Prefeitura"                                                                 );
$obCmbEntidade->addOption       ( "C", "C - Câmara"                                                                     );
$obCmbEntidade->addOption       ( "D", "D - Descentralizada"                                                            );
$obCmbEntidade->setValue        ( $inCodEntidade                                                                        );
$obCmbEntidade->setNull         ( false                                                                                 );

//Número da Entidade
$obTxtNumeroEntidade = new TextBox;
$obTxtNumeroEntidade->setRotulo          ( "Número da Entidade"                                                         );
$obTxtNumeroEntidade->setName            ( "inNumEntidade"                                                              );
$obTxtNumeroEntidade->setValue           ( $inNumEntidade                                                               );
$obTxtNumeroEntidade->setTitle           ( "Informe o número da Entidade, conforme fornecido pelo TCM."                 );
$obTxtNumeroEntidade->setSize            ( 11                                                                           );
$obTxtNumeroEntidade->setMaxLength       ( 9                                                                           );
$obTxtNumeroEntidade->setInteiro         ( true                                                                         );
$obTxtNumeroEntidade->setNull            ( false                                                                        );

$obTIMATipoServidor = new TIMATipoServidor();
$obTIMATipoServidor->recuperaTodos($rsTipoServidor);

while (!$rsTipoServidor->eof()) {
    switch ($rsTipoServidor->getCampo("cod_tipo_servidor")) {
        case 1:
            $stTitleRegime = 'Selecione o(s) regimes para classificar os servidores Efetivos.';
            $stTitleSubDivisao = 'Selecione a(s) subdivisões dos regimes para classificar os servidores Efetivos.';
        break;
        case 2:
            $stTitleRegime = 'Selecione o(s) regimes para classificar os servidores Celetistas.';
            $stTitleSubDivisao = 'Selecione a(s) subdivisões dos regimes para classificar os servidores Celetistas.';
        break;
        case 3:
            $stTitleRegime = 'Selecione o(s) regimes para classificar os servidores do tipo Cargo em Comissão.';
            $stTitleSubDivisao = 'Selecione a(s) subdivisões dos regimes para classificar os servidores do tipo Cargo em Comissão.';
        break;
        case 5:
            $stTitleRegime = 'Selecione o(s) regimes para classificar os servidores Temporários.';
            $stTitleSubDivisao = 'Selecione a(s) subdivisões dos regimes para classificar os servidores Temporários.';
        break;
        case 6:
            $stTitleRegime = 'Selecione o(s) regimes para classificar os servidores do tipo Agentes Políticos.';
            $stTitleSubDivisao = 'Selecione a(s) subdivisões dos regimes para classificar os servidores do tipo Agentes Políticos.';
        break;
    }

    $stNomeComponenteRegime     = "obISelectMultiploRegime".$rsTipoServidor->getCampo("cod_tipo_servidor");
    $stNomeComponenteSubDivisao = "obISelectMultiploSubDivisao".$rsTipoServidor->getCampo("cod_tipo_servidor");

    $$stNomeComponenteRegime = new ISelectMultiploRegime( $rsTipoServidor->getCampo("cod_tipo_servidor") );
    $$stNomeComponenteRegime->obCmbRegime->setTitle( $stTitleRegime );
    $stOnClick = "ajaxJavaScript( '".CAM_GRH_IMA_INSTANCIAS."configuracao/OCManterConfiguracaoTCMBA.php?stServidor=".$rsTipoServidor->getCampo("cod_tipo_servidor")."&".Sessao::getId()."'+selectMultiploToString( inCodRegimeSelecionados".$rsTipoServidor->getCampo("cod_tipo_servidor")." ) , 'preencherSubDivisao' );";
    $$stNomeComponenteRegime->setFuncaoOnClick( $stOnClick );

    $$stNomeComponenteSubDivisao = new ISelectMultiploSubDivisao( $rsTipoServidor->getCampo("cod_tipo_servidor") );
    $$stNomeComponenteSubDivisao->obCmbSubDivisao->setTitle( $stTitleSubDivisao );
    $$stNomeComponenteSubDivisao->obCmbSubDivisao->setNull ( false );
    $rsTipoServidor->proximo();
}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo             ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden             ( $obHdnAcao                            											);
$obFormulario->addHidden             ( $obHdnCtrl                                                                       );
$obFormulario->addTitulo             ( "Configuração da Exportação TCM/BA" 										    	);
$obFormulario->addComponente         ( $obCmbEntidade             										        	    );
$obFormulario->addComponente         ( $obTxtNumeroEntidade              										        );
$rsTipoServidor->setPrimeiroElemento();
while (!$rsTipoServidor->eof()) {
    $obFormulario->addTitulo             ( $rsTipoServidor->getCampo("descricao")                                                                    );

    $stNomeComponenteRegime     = "obISelectMultiploRegime".$rsTipoServidor->getCampo("cod_tipo_servidor");
    $stNomeComponenteSubDivisao = "obISelectMultiploSubDivisao".$rsTipoServidor->getCampo("cod_tipo_servidor");

    $$stNomeComponenteRegime->geraFormulario( $obFormulario  );
    $$stNomeComponenteSubDivisao->geraFormulario( $obFormulario                                                      );
    $rsTipoServidor->proximo();
}

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
