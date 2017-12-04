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
    * Página de processamento para popup baixa de debito automatica
    * Data de criação : 24/03/2006

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Marcelo B. Paulino

    * $Id: OCRelatorioBaixaAutomatica.php 65763 2016-06-16 17:31:43Z evandro $

    Caso de uso: uc-05.03.10
**/

/*
$Log$
Revision 1.3  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorioAgata.class.php" );

;

$obAgata = new RRelatorioAgata( CAM_GT_AGT_ARR."relatBaixa.agt" );
$obAgata->setParameter( '$inCodLote'             , $_GET['cod_lote']  );
$obAgata->setParameter( '$inExercicio'           , $_GET['exercicio'] );

//buscar informaçoes da prefeitura
include_once(CLA_MASCARA_CNPJ);
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
$obTConfiguracao = new TAdministracaoConfiguracao;
$obMascaraCNPJ   = new MascaraCNPJ;

$stChave =  $obTConfiguracao->getComplementoChave();
$obTConfiguracao->setComplementoChave("parametro,cod_modulo");
$arPropriedades = array( "nom_prefeitura" => "","cnpj" => "" ,"fone" => "", "fax" => "", "e_mail" => "","logradouro" => "",
                         "numero" => "", "nom_municipio" => "", "cep" => "" , "logotipo" => "","tipo_logradouro" => ""
                        );

$obTConfiguracao->setDado( "exercicio" , $sessao>exercicio );
foreach ($arPropriedades as $stParametro => $stValor) {
    $obErro = $obTConfiguracao->pegaConfiguracao($stValor, $stParametro );
    $arConfiguracao[$stParametro] = $stValor;
    if ( $obErro->ocorreu() ) {
        break;
    }
}
$obMascaraCNPJ->mascaraDado( $arConfiguracao['cnpj'] );

$obAgata->setParameter( '$prefeitura'            ,$arConfiguracao['nom_prefeitura'] );
$obAgata->setParameter( '$fone'                  ,$arConfiguracao['fone']           );
$obAgata->setParameter( '$fax'                   ,$arConfiguracao['fax']            );
$obAgata->setParameter( '$mail'                  ,$arConfiguracao['e_mail']         );
$obAgata->setParameter( '$endereco'              ,$arConfiguracao['tipo_logradouro']." ".$arConfiguracao['logradouro']." ".$arConfiguracao['numero'] );
$obAgata->setParameter( '$cep'                   ,$arConfiguracao['cep']            );
$obAgata->setParameter( '$usuario'               ,Sessao::getUsername()                 );
$obAgata->setParameter( '$imagem'                ,$arConfiguracao['logotipo']       );
$obAgata->setParameter( '$cnpj'                  ,$arConfiguracao['cnpj']           );

$obTAcao         = new TAdministracaoAcao;
$stFiltro = " AND A.cod_acao = ".Sessao::read('acao');
$obErro = $obTAcao->recuperaRelacionamento( $rsRecordSet, $stFiltro, ' A.cod_acao ', $boTransacao );
if ( !$obErro->ocorreu() ) {
    $arConfiguracao[ "cod_modulo" ]         = $rsRecordSet->getCampo( "cod_modulo" );
    $arConfiguracao[ "cod_funcionalidade" ] = $rsRecordSet->getCampo( "cod_funcionalidade" );
    $arConfiguracao[ "cod_gestao" ] = 5;
}

$obAgata->setParameter( '$cod_acao'              , Sessao::read('acao')                    );
$obAgata->setParameter( '$cod_gestao'            , $arConfiguracao['cod_gestao']         );
$obAgata->setParameter( '$cod_modulo'            , $arConfiguracao['cod_modulo']         );
$obAgata->setParameter( '$cod_funcionalidade'    , $arConfiguracao['cod_funcionalidade']         );

$ok = $obAgata->generateDocument();
if (!$ok) {
    echo $obAgata->getError();
} else {
    $obAgata->fileDialog();
}
