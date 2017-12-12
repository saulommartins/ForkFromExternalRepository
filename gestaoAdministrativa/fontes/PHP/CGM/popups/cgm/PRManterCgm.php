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
    * Página de Processamento
    * Data de Criação   : 11/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    * Casos de uso: uc-03.03.04

    $Id: PRManterCatalogo.php 34555 2008-10-16 15:10:07Z luiz $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");
include_once(CAM_GA_CGM_MAPEAMENTO."TCGMLogradouro.class.php");
include_once(CAM_GA_CGM_MAPEAMENTO."TCGMLogradouroCorrespondencia.class.php");
include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php");
include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php");
include_once(CAM_GA_CGM_MAPEAMENTO."TCGMAtributoValor.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoLogradouro.class.php");

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterCgm";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";

include_once 'JS'.$stPrograma.".js";

Sessao::setTrataExcecao( true );

$stNomCgm = ($_POST['boPessoa']=='fisica') ? $_POST['stNome'] : $_POST['stRazaoSocial'];
$_POST['inCodMunicipioCor'] = $_POST['inCodMunicipioCor'] ? $_POST['inCodMunicipioCor'] : '0';
$_POST['inCodEstadoCor']    = $_POST['inCodEstadoCor']    ? $_POST['inCodEstadoCor']    : '0';

$rsLogradouro = $rsLogradouroCor = new RecordSet();

$stFiltroLogradouro = " AND L.cod_logradouro=".$_POST['inNumLogradouro'];
$obTLogradouro = new TLogradouro;
$obTLogradouro->recuperaRelacionamento($rsLogradouro,$stFiltroLogradouro);

if ($_POST['inNumLogradouroCor']) {
    $stFiltroLogradouro = " AND L.cod_logradouro=".$_POST['inNumLogradouroCor'];
    $obTLogradouro = new TLogradouro;
    $obTLogradouro->recuperaRelacionamento($rsLogradouroCor,$stFiltroLogradouro);
}

$obTCGM = new TCGM();
$obTCGM->setDado('cod_municipio'            ,$_POST['inCodMunicipio']);
$obTCGM->setDado('cod_uf'                   ,$_POST['inCodEstado']);
$obTCGM->setDado('cod_pais'                 ,$_POST['pais']);
$obTCGM->setDado('cod_municipio_corresp'    ,$_POST['inCodMunicipioCor']);
$obTCGM->setDado('cod_uf_corresp'           ,$_POST['inCodEstadoCor']);
$obTCGM->setDado('cod_pais_corresp'         ,$_POST['paisCor']);
$obTCGM->setDado('cod_responsavel'          ,Sessao::read('numCgm') );
$obTCGM->setDado('nom_cgm'                  ,$stNomCgm );
$obTCGM->setDado('tipo_logradouro'          ,$rsLogradouro->getCampo('nom_tipo') );
$obTCGM->setDado('logradouro'               ,$rsLogradouro->getCampo('nom_logradouro') );
$obTCGM->setDado('numero'                   ,$_POST['inNumero']);
$obTCGM->setDado('complemento'              ,$_POST['stComplemento']);
$obTCGM->setDado('bairro'                   ,$rsLogradouro->getCampo('nom_bairro'));
$obTCGM->setDado('cep'                      ,$_POST['cmbCEP']);
$obTCGM->setDado('tipo_logradouro_corresp'  ,$rsLogradouroCor->getCampo('nom_tipo') );
$obTCGM->setDado('logradouro_corresp'       ,$rsLogradouroCor->getCampo('nom_logradouro') );
$obTCGM->setDado('numero_corresp'           ,$_POST['inNumeroCor'] );
$obTCGM->setDado('complemento_corresp'      ,$_POST['stComplementoCor']);
$obTCGM->setDado('bairro_corresp'           ,$rsLogradouro->getCampo('nom_bairro'));
$obTCGM->setDado('cep_corresp'              ,$_POST['cmbCEPCor']);
$obTCGM->setDado('fone_residencial'         ,$_POST['inDDDTelResidencial'].$_POST['inTelResidencial']);
$obTCGM->setDado('ramal_residencial'        ,$_POST['???']);
$obTCGM->setDado('fone_comercial'           ,$_POST['inDDDTelComercial'].$_POST['inTelComercial']);
$obTCGM->setDado('ramal_comercial'          ,$_POST['inTelComercialRamal']);
$obTCGM->setDado('fone_celular'             ,$_POST['inDDDTelCelular'].$_POST['inTelCelular']);
$obTCGM->setDado('e_mail'                   ,$_POST['stEmail']);
$obTCGM->setDado('e_mail_adcional'          ,$_POST['stEmailAdicional']);
$obTCGM->proximoCod( $inNumCGM );
$obTCGM->setDado('numcgm'                   ,$inNumCGM);
$obTCGM->inclusao();

$obTCGMLogradouro = new TCGMLogradouro;
$obTCGMLogradouro->setDado('numcgm'                   ,$inNumCGM);
$obTCGMLogradouro->setDado('cod_logradouro'           ,$_POST['inNumLogradouro']);
$obTCGMLogradouro->setDado('cod_municipio'            ,$_POST['inCodMunicipio']);
$obTCGMLogradouro->setDado('cod_uf'                   ,$_POST['inCodEstado']);
$obTCGMLogradouro->setDado('cod_bairro'               ,$_POST['cmbBairro']);
$obTCGMLogradouro->setDado('cep'                      ,$_POST['cmbCEP']);
$obTCGMLogradouro->inclusao();

if ($_POST['inNumLogradouroCor'] and $_POST['cmbBairroCor'] and $_POST['cmbCEPCor']) {
    $obTCGMLogradouroCor = new TCGMLogradouroCorrespondencia;
    $obTCGMLogradouroCor->setDado('numcgm'                   ,$inNumCGM);
    $obTCGMLogradouroCor->setDado('cod_logradouro'           ,$_POST['inNumLogradouroCor']);
    $obTCGMLogradouroCor->setDado('cod_municipio'            ,$_POST['inCodMunicipioCor']);
    $obTCGMLogradouroCor->setDado('cod_uf'                   ,$_POST['inCodEstadoCor']);
    $obTCGMLogradouroCor->setDado('cod_bairro'               ,$_POST['cmbBairroCor']);
    $obTCGMLogradouroCor->setDado('cep'                      ,$_POST['cmbCEPCor']);
    $obTCGMLogradouroCor->inclusao();
}

if ($_POST['boPessoa']=='fisica') {
    $obTCGMPessoaFisica = new TCGMPessoaFisica;
    $obTCGMPessoaFisica->setDado('numcgm'                   ,$inNumCGM);
    if( $_POST['stCPF'] )
        $obTCGMPessoaFisica->setDado('cpf'                      ,preg_replace('/[^a-zA-Z0-9]/','', $_POST['stCPF']) );
    $obTCGMPessoaFisica->setDado('rg'                       ,$_POST['stRG']);
    $obTCGMPessoaFisica->setDado('orgao_emissor'            ,$_POST['stOrgaoEmissor']);
    $obTCGMPessoaFisica->setDado('cod_uf_orgao_emissor'     ,$_POST['inCodUF']);
    $obTCGMPessoaFisica->setDado('dt_emissao_rg'            ,$_POST['stDataEmissao']);
    $obTCGMPessoaFisica->setDado('num_cnh'                  ,$_POST['stCNH']);
    $obTCGMPessoaFisica->setDado('dt_validade_cnh'          ,$_POST['stDataValidade']);
    $obTCGMPessoaFisica->setDado('cod_categoria_cnh'        ,$_POST['inCodCategoriaHabilitacao']);
    $obTCGMPessoaFisica->setDado('cod_nacionalidade'        ,$_POST['inCodNacionalidade']);
    $obTCGMPessoaFisica->setDado('cod_escolaridade'         ,$_POST['inCodEscolaridade']);
    $obTCGMPessoaFisica->setDado('dt_nascimento'            ,$_POST['stDataNascimento']);
    $obTCGMPessoaFisica->setDado('sexo'                     ,$_POST['stSexo']);
    $obTCGMPessoaFisica->setDado('servidor_pis_pasep'       ,$_POST['stPisPasep']);
    $obTCGMPessoaFisica->inclusao();
} else {
    $obTCGMPessoaJuridica = new TCGMPessoaJuridica;
    $obTCGMPessoaJuridica->setDado('numcgm'                   ,$inNumCGM);
    $obTCGMPessoaJuridica->setDado('cnpj'                     ,$_POST['stCNPJ']);
    $obTCGMPessoaJuridica->setDado('insc_estadual'            ,$_POST['stInscricaoEstadual']);
    $obTCGMPessoaJuridica->setDado('nom_fantasia'             ,$_POST['stNomeFantasia']);
    $obTCGMPessoaJuridica->inclusao();

}

foreach ($_POST['atributo'] as $inCodAtributo=>$mxValor) {
    $obTCGMAtributoValor = new TCGMAtributoValor;
    $obTCGMAtributoValor->setDado('numcgm'          ,$inNumCGM);
    $obTCGMAtributoValor->setDado('cod_atributo'    ,$inCodAtributo);
    $obTCGMAtributoValor->setDado('valor'           ,$mxValor);
    $obTCGMAtributoValor->inclusao();
}

if (!$stErro) {
    //SistemaLegado::alertaAviso($pgForm, "$inNumCGM - $stNomCgm","incluir","aviso", Sessao::getId(), "../");
    echo "<script>insereCGMpopup('$inNumCGM','$stNomCgm');</script>";
} else {
    SistemaLegado::exibeAviso(urlencode($stErro),"n_incluir","erro");
}
Sessao::encerraExcecao();

?>
