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
  * Página de processamento para calculo
  * Data de criação : 02/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: PREmitirCarne.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    Caso de uso: uc-05.03.21
**/

/*
$Log$
Revision 1.3  2007/04/16 18:05:52  cercato
Bug #9132#

Revision 1.2  2006/10/18 19:02:27  cercato
correcao do caso de uso.

Revision 1.1  2006/10/10 15:17:57  cercato
*** empty log message ***

Revision 1.2  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

$obRARRCarne->listarEmissaoCarne($rsEmissaoCarne);

if ( $rsEmissaoCarne->getNumLinhas() > 0 ) {
    $boExec = TRUE;
    $arEmissao = array();
    $rsEmissaoCarne->setPrimeiroElemento();

    $arArqMod = explode( "§", $_REQUEST["stArquivo"] );
    $stArquivoModelo = $arArqMod[0];
    $inCodModelo = $arArqMod[1];

    while ( !$rsEmissaoCarne->eof() ) {
        $arEmissao[$rsEmissaoCarne->getCampo('cod_lancamento')][] = array(
            "numeracao" => $rsEmissaoCarne->getCampo("numeracao"),
            "inscricao" => $rsEmissaoCarne->getCampo('inscricao'),
            "cod_parcela" => $rsEmissaoCarne->getCampo('cod_parcela'),
            "exercicio" => $rsEmissaoCarne->getCampo('exercicio'),
            "numcgm" => $rsEmissaoCarne->getCampo('numcgm'),
            "cod_modelo" => $inCodModelo
        );

        $rsEmissaoCarne->proximo();
    }

    /**
    *   grava nome pdf e parametro para salvar em disco
    *   usado tambem no objeto pdf
    */

    Sessao::write ( "stNomPdf", ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf" );
    Sessao::write ( "stParamPdf", "F" );

    $arTmp = explode( ".", $stArquivoModelo );
    $stObjModelo = $arTmp[0];

    include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );

    $obRModeloCarne = new $stObjModelo( $arEmissao );
    $obRModeloCarne->imprimirCarne();
/*
    if ($boDiversas) {
        include_once( CAM_GT_ARR_CLASSES."boletos/RDiversosPetropolis.class.php" );
        $obRRelatorioCarnePetropolis = new RDiversosPetropolis( $arEmissao );
        $obErro = $obRRelatorioCarnePetropolis->imprimirCarne();
    } else {
        include_once( CAM_GT_ARR_CLASSES."boletos/RRelatorioCarnePetropolis.class.php" );
        $obRRelatorioCarnePetropolis = new RRelatorioCarnePetropolis( $arEmissao );
        // sendo modelo do iptu, envia imovel
        $obRRelatorioCarnePetropolis->obRARRCarne = $obRARRCarne;
        $sessao->transf4['itbi_observacao'] = 'sim';
        $obErro = $obRRelatorioCarnePetropolis->imprimirCarne( $boEspecial );
    }
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

    include_once (CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php");
    $rsEmissaoCarne->setPrimeiroElemento();
    while ( !$rsEmissaoCarne->eof() ) {
        $obTARRCarne = new TARRCarne;
        $obTARRCarne->setDado ( "numeracao"     , $rsEmissaoCarne->getCampo('numeracao')        );
        $obTARRCarne->setDado ( "cod_convenio"  , $rsEmissaoCarne->getCampo('cod_convenio')     );
        $obTARRCarne->setDado ( "cod_parcela"   , $rsEmissaoCarne->getCampo('cod_parcela')      );
        $obTARRCarne->setDado ( "exercicio"     , $rsEmissaoCarne->getCampo('exercicio')        );
        $obTARRCarne->setDado ( "impresso"      , TRUE                                          );
        $obErro = $obTARRCarne->alteracao();

        $rsEmissaoCarne->proximo();
    }

if (!$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso( $pgFilt."?stAcao=incluir","","incluir","aviso", Sessao::getId(), "../" );
} else {
    SistemaLegado::alertaAviso($pgForm."?stAcao=emitir",urlencode($obErro->getDescricao()),"n_incluir","erro",Sessao::getId(),"../");
}

if ($boExec) {
    echo "<script type=\"text/javascript\">\r\n";
    echo "    var sAux = window.open('".CAM_GT_ARR_INSTANCIAS."documentos/OCImpressaoPDFEmissao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
    echo "    eval(sAux)\r\n";
//    echo "parent.document.getElementById(\"oculto\").src=\"".CAM_GT_ARR_INSTANCIAS."calculo/OCImpressaoPDFEmissao.php?".Sessao::getId()."\";\r\n";
    echo "</script>\r\n";
}

}
