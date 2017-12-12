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
    * Página de Frame Oculto de Emissao
    * Data de Criação   : 16/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCManterEmissao.php 39946 2009-04-29 12:14:30Z cercato $

    * Casos de uso: uc-05.02.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMEmissaoDocumento.class.php" );
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaImovelArea.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAreaConstrucao.class.php" );

switch ($_REQUEST['stCtrl']) {
    case "Download":
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php" );
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"       );
        include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicenca.class.php" );
        include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeCadastroEconomico.class.php" );
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php" );
        include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoEdificacaoValor.class.php" );
        include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoConstrucaoOutrosValor.class.php" );

        $obTConfiguracao    = new TAdministracaoConfiguracao;
        $obTMunicipio       = new TMunicipio;
        $obTUF              = new TUF;

        include_once ( CAM_OOPARSER."tbs_class.php" );
        include_once ( CAM_OOPARSER."tbsooo_class.php" );

        //PEGANDO INFORMACOES DA PREFEITURA
        $inArquivo = $_REQUEST["HdnQual"];

        if ($_REQUEST['stOrigemFormulario'] == 'consulta') {

            $inInscricaoImobiliaria   = $_REQUEST['inInscricaoImobiliaria'];
            $inCodLicenca           = $_REQUEST['inCodLicenca'];
            $inExercicio            = $_REQUEST['inExercicio'];
            $stNomeArquivoTemplate  = $_REQUEST['stNomeArquivoTemplate'];
            $stTipoLicenca          = 'Licenca '.$_REQUEST['stTipoLicenca'];
            $inCodDocumento         = $_REQUEST['inCodigoDocumento'];
            $inCodTipoDocumento     = $_REQUEST['inCodigoTipoDocumento'];
            $stNomDocumento         = $_REQUEST['stNomDocumento'];
            $flAreaLicenca	    = $_REQUEST['flAreaLicenca'];
            $stProcesso		    = $_REQUEST['stProcesso'];
            $inCodAtributoEstrutura = $_REQUEST['inCodAtributoEstrutura'];
            $inCodAtributo = $_REQUEST['inCodAtributo'];
            $stTipoLicencaUnidade   = $_REQUEST['stTipoLicencaUnidade'];
            $inCodConstrucao        = $_REQUEST['inCodConstrucao'];

        } else {
            $arDadosSessao = Sessao::read( "dados" );
            $inInscricaoImobiliaria   = $arDadosSessao[$inArquivo]['inInscricaoImobiliaria'];
            $inCodLicenca           = $arDadosSessao[$inArquivo]['inCodigoLicenca'];
            $inExercicio            = $arDadosSessao[$inArquivo]['inExercicio'];
            $stNomeArquivoTemplate  = $arDadosSessao[$inArquivo]['stNomeArquivoTemplate'];
            $stTipoLicenca          = $arDadosSessao[$inArquivo]['stTipoLicenca'];
            $inCodDocumento         = $arDadosSessao[$inArquivo]['inCodigoDocumento'];
            $inCodTipoDocumento     = $arDadosSessao[$inArquivo]['inCodigoTipoDocumento'];
            $stNomDocumento         = $arDadosSessao[$inArquivo]['stNomeDocumento'];
            $flAreaLicenca          = $arDadosSessao[$inArquivo]['flAreaLicenca'];
            $stProcesso             = $arDadosSessao[$inArquivo]['stProcesso'];
            $inCodAtributoEstrutura = $arDadosSessao[$inArquivo]['inCodAtributoEstrutura'];
            $inCodAtributo          = $arDadosSessao[$inArquivo]['inCodAtributo'];
            $stTipoLicencaUnidade   = $arDadosSessao[$inArquivo]['stTipoLicencaUnidade'];
            $inCodConstrucao        = $arDadosSessao[$inArquivo]['inCodConstrucao'];
        }

        $stNomeArquivoDownload  = str_replace ( ' ', '_', $stNomDocumento );
        $stNomeArquivoDownload .= '_'.$inCodLicenca.'_'.$inExercicio.'.odt';

        $stChave =  $obTConfiguracao->getComplementoChave();
        $obTConfiguracao->setComplementoChave("parametro,cod_modulo");
        $arPropriedades = array( "nom_prefeitura" => "","cnpj" => "" ,"fone" => "", "fax" => "",
                                "e_mail" => "", "logradouro" => "", "numero" => "", "sigla_uf" => "",
                                "cep" => "" , "logotipo" => "", "cod_uf" => "", "nom_uf" => "",
                                "cod_municipio" => "", "nom_municipio" => "", "CGMPrefeito" => "" );
        $obTConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
        foreach ($arPropriedades as $stParametro => $stValor) {
            $stFiltro = " WHERE exercicio = ".Sessao::getExercicio()." AND parametro = '".$stParametro."'";
            $obErro = $obTConfiguracao->recuperaTodos( $rsDados, $stFiltro );
            $stValor = $rsDados->getCampo("valor");
            $arConfiguracao[0][$stParametro] = $stValor;
            if ( $obErro->ocorreu() ) {
                break;
            }
        }

        $obTConfiguracao->setComplementoChave( $stChave );
        $stFiltroMunicipio = " WHERE sw_municipio.cod_uf = ".$arConfiguracao[0]['cod_uf'];
        $stFiltroMunicipio .=" and sw_municipio.cod_municipio = ".$arConfiguracao[0]['cod_municipio'];
        $obTMunicipio->recuperaTodos( $rsMunicipio, $stFiltroMunicipio );

        $stFiltroUF = " WHERE sw_uf.cod_uf = ".$arConfiguracao[0]['cod_uf'];
        $obTUF->recuperaTodos( $rsUF, $stFiltroUF );

        $arConfiguracao[0]['nom_municipio'] = $rsMunicipio->getCampo('nom_municipio');
        $arConfiguracao[0]['nom_uf'] = $rsUF->getCampo('nom_uf');
        $arConfiguracao[0]['sigla_uf'] = $rsUF->getCampo('sigla_uf');

        $rsConfiguracao = new RecordSet;
        $rsConfiguracao->preenche ( $arConfiguracao );

        //===================== INSERE NA TABELA EMISSAO DOCUMENTO
        include_once( CAM_GT_CIM_MAPEAMENTO."TCIMEmissaoDocumento.class.php");
        $obTCIMEmissaoDocumento = new TCIMEmissaoDocumento;

        $stFiltro = " WHERE cod_licenca = ".$inCodLicenca;
        $obTCIMEmissaoDocumento->recuperaUltimoRegistro( $rsEmissao, $stFiltro );
        $inNumEmissao = ( $rsEmissao->getCampo('valor') + 1 );

        // DEVE-SE TER O VALOR CORRESPONDENTE AO DIRETOR TRIBUTOS NA CONFIGURACAO
        $obTConfiguracao->setDado ( 'cod_modulo', 14 );
        $obTConfiguracao->setDado ( 'parametro', 'diretor_tributos' );
        $obTConfiguracao->setDado ( 'exercicio', Sessao::getExercicio() );
        $obTConfiguracao->recuperaPorChave ( $rsConfiguracao );
        $cgm_diretor_tributos = $rsConfiguracao->getCampo ('valor');

        if (!$inExercicio) {
            $inExercicio = '0000';
        }
        $rsLicencaIM = new RecordSet;
        $obTCIMImovel = new TCIMImovel;
        $obTCIMImovel->recuperaRelacionamento($rsImovel, " WHERE inscricao_municipal = ".$inInscricaoImobiliaria. " \n");

        //BUSCA DO TIMESTAMP NA TABELA LICENCA_DOCUMENTO
        include_once( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaDocumento.class.php");
        $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
        $stFiltroLicencaDocumento = " WHERE cod_licenca = ".$inCodLicenca." AND exercicio = ".$inExercicio." AND cod_documento = ".$inCodDOcumento." AND cod_tipo_documento = ".$inCodTipoDocumento."";
        $obTCIMLicencaDocumento->recuperaTodos($rsLicencaDocumento, $stFiltro);

        foreach ($rsLicencaDocumento->arElementos as $key) {
            $stTimestampLicenca = $key['timestamp'];
        }
        $obTCIMEmissaoDocumento->setDado ( "cod_licenca"         , $inCodLicenca );
        $obTCIMEmissaoDocumento->setDado ( "exercicio"           , $inExercicio );
        $obTCIMEmissaoDocumento->setDado ( "numcgm", Sessao::read('numCgm') );
        $obTCIMEmissaoDocumento->setDado ( "dt_emissao", date ("d/m/Y") );
        $obTCIMEmissaoDocumento->setDado ( "timestamp", $stTimestampLicenca);

        $obTCIMEmissaoDocumento->recuperaTodos($rsEmissaoDoc, " WHERE cod_licenca = ".$inCodLicenca." AND exercicio = '".$inExercicio."' AND numcgm = ".Sessao::read('numCgm')." AND timestamp = '".$stTimestampLicenca."' AND dt_emissao = '".date ("Y/m/d")."'");

        if ($rsEmissaoDoc->getNumLinhas() > 0) {
            $obTCIMEmissaoDocumento->alteracao();
        } else {
            $obTCIMEmissaoDocumento->inclusao();
        }

        // RECUPERA OS DADOS DA INSCRICAO IMOBILIARIA

        $obCGMResponsavel = new TCGM;
        $obCGMResponsavel->recuperaRelacionamento($rsCGM, " WHERE CGM.numcgm = ".$rsImovel->getCampo("numcgm")."");

        $obTCIMLicenca = new TCIMLicenca;
        $obTCIMLicenca->recuperaTodos($rsLicenca, " WHERE exercicio = '".$inExercicio."' AND cod_licenca = ".$inCodLicenca."");
        $rsLicenca->setCampo( 'data_emissao', date('d/m/y') );

        if ( $rsCGM->getCampo("cpf") != "" ) {
            $stCPF = $rsCGM->getCampo("cpf");
            $obMascara = new MascaraCPF;
            $obMascara->setDesmascarado( $stCPF );
            $obMascara->mascaraDinamica();
            $stCPF = $obMascara->getMascarado();
            $rsCGM->setCampo ('cpf_cnpj', $stCPF );
            $rsCGM->setCampo ('boCpf_cnpj', 'CPF');
        } else {
            $stCNPJ = $rsCGM->getCampo("cnpj");
            if (isset($stCNPJ)) {
                if ($stCNPJ) {
                    $cnpj1 = substr( $stCNPJ, 0, 2 );
                    $cnpj2 = substr( $stCNPJ, 2, 3 );
                    $cnpj3 = substr( $stCNPJ, 5, 3 );
                    $cnpj4 = substr( $stCNPJ, 8, 4 );
                    $cnpj5 = substr( $stCNPJ, 12, 2 );
                    $stCNPJ = $cnpj1.".".$cnpj2.".".$cnpj3."/".$cnpj4."-".$cnpj5;
                    $rsCGM->setCampo ('cpf_cnpj', $stCNPJ );
                    $rsCGM->setCampo ('boCpf_cnpj', 'CNPJ');
                }
            } elseif (($rsCGM->getCampo("cpf") == "") && (!isset($stCNPJ))) {

                $rsCGM->setCampo ('cpf_cnpj', "" );
                $rsCGM->setCampo ('boCpf_cnpj', 'CPF');
            }
        }

        $obTCIMAreaConstrucao = new TCIMAreaConstrucao;
        $obTCIMAreaConstrucao->recuperaAreaConstrucao($rsAreaConstrucao, 'inscricao_municipal = '.$inInscricaoImobiliaria.'');

        $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico;
        $obTAdministracaoAtributoDinamico->recuperaTodos ( $rsAtributoDinamico, " WHERE nom_atributo ilike 'CE -%'" );

        if ($stTipoLicencaUnidade == 'edificacao') {
            $obLicencaUnidade = new TCIMAtributoTipoEdificacaoValor;
            $obLicencaUnidade->recuperaAtributoValor ( $rsLicencaUnidade, ' WHERE atributo_tipo_edificacao_valor.cod_construcao = '.$inCodConstrucao.'
                                                                              AND atributo_valor_padrao.cod_valor = '.$inCodAtributoEstrutura.'
                                                                              AND atributo_valor_padrao.cod_atributo ='.$inCodAtributo.'');

        } elseif ($stTipoLicencaUnidade == 'construcao') {
            $obLicencaUnidade = new TCIMAtributoConstrucaoOutrosValor;
            $obLicencaUnidade->recuperaAtributoValor ( $rsLicencaUnidade, ' WHERE atributo_construcao_outros_valor.cod_construcao = '.$inCodConstrucao.'
                                                                              AND atributo_valor_padrao.cod_valor = '.$inCodAtributoEstrutura.'');
        }

        if ($obLicencaUnidade) {

            foreach ($rsLicencaUnidade as $valueUnidades) {
                if (is_array($valueUnidades)) {
                    foreach ($valueUnidades as $valueUnidade) {
                        $stValorPadrao = $valueUnidade['valor_padrao'];
                    }
                }
//                foreach ($rsAtributoDinamico as $keyAtributo => $valueAtributo) {
//                    if ($valueUnidade['cod_atributo'] == $valueAtributo['cod_atributo']) {
//                    $stValorPadrao = $valueUnidade['valor_padrao'];
//                    }
//                }
            }
        }

        //$obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico;
        //$obTAdministracaoAtributoDinamico->recuperaAtributosValor ( $rsAtributosValor, ' WHERE atributo_valor_padrao.cod_atributo = '.$_REQUEST['inCodAtributoEstrutura'].'' );

        //LOOP PARA INSERIR DADOS NO RECORDSET Licenca (para IE)
        foreach ($rsLicenca->arElementos as $index => $key) {
            //necessário dividir a data de emissão para apresentar no arquivo do alvará
            $dataFrag = explode('/', $key['data_emissao']);
            switch ($dataFrag[1]) {
            case '01':
                $dataFrag[1] = "janeiro";
                break;
            case '02':
                $dataFrag[1] = "fevereiro";
                break;
            case '03':
                $dataFrag[1] = "março";
                break;
            case '04':
                $dataFrag[1] = "abril";
                break;
            case '05':
                $dataFrag[1] = "maio";
                break;
            case '06':
                $dataFrag[1] = "junho";
                break;
            case '07':
                $dataFrag[1] = "julho";
                break;
            case '08':
                $dataFrag[1] = "agosto";
                break;
            case '09':
                $dataFrag[1] = "setembro";
                break;
            case '10':
                $dataFrag[1] = "outubro";
                break;
            case '11':
                $dataFrag[1] = "novembro";
                break;
            case '12':
                $dataFrag[1] = "dezembro";
                break;
            }

            $rsLicenca->arElementos[$index]['dt_emissao_dia'] = $dataFrag[0];
            $rsLicenca->arElementos[$index]['dt_emissao_mes'] = $dataFrag[1];
            $rsLicenca->arElementos[$index]['area_licenca'] = $flAreaLicenca;
            $rsLicenca->arElementos[$index]['area_total'] = $rsAreaConstrucao->getCampo('area_total');
            $rsLicenca->arElementos[$index]['processo'] = $stProcesso;
            $rsLicenca->arElementos[$index]['atributo'] = $stValorPadrao;
        }

        // instantiate a TBS OOo class
        $OOParser = new clsTinyButStrongOOo;

        // setting the object
        $OOParser->SetZipBinary('zip');
        $OOParser->SetUnzipBinary('unzip');
        $OOParser->SetProcessDir('/tmp');

        $stDocumentoPath = '/tmp/';
        $OOParser->_process_path = $stDocumentoPath; //nome do arquivo pra salva

        // create a new openoffice document from the template with an unique id
        $stArquivo = CAM_GT_CIM_ANEXOS ."alvara/".$stNomeArquivoTemplate;

        $OOParser->NewDocFromTpl( $stArquivo );
        $OOParser->LoadXmlFromDoc('content.xml');
        $OOParser->MergeBlock( 'Conf',  $rsConfiguracao->arElementos );
        $OOParser->MergeBlock( 'IE',    $rsCGM->arElementos );
        $OOParser->MergeBlock( 'IM',    $rsImovel->arElementos );
        $OOParser->MergeBlock( 'LI',    $rsLicenca->arElementos );
        $OOParser->SaveXmlToDoc();
        $OOParser->LoadXmlFromDoc('styles.xml');
        $OOParser->SaveXmlToDoc();

        $typefile = 'application/sxw';
        header("Content-type: $typefile");//$OOParser->GetMimetypeDoc(). 'name='.$stNomeArquivoDownload );
        header('Content-Length: '.filesize($OOParser->GetPathnameDoc()));
        header("Content-Disposition: attachment; filename=\"$stNomeArquivoDownload\"");
        readfile($OOParser->GetPathnameDoc());
        //$OOParser->FlushDoc();
        //$OOParser->RemoveDoc();

    break;
}
