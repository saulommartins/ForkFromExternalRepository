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
  * Página de Processamento Avaliar Imóvel
  * Data de criação : 14/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @ignore

    * $Id: PRAvaliarImovel.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.20  2006/10/10 15:18:56  cercato
alterando formularios para retirar ITBI.

Revision 1.19  2006/09/18 17:31:42  domluc
*** empty log message ***

Revision 1.18  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoImobiliaria.class.php");
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php");
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php");

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( 'link' );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "AvaliarImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//SistemaLegado::debugRequest();exit;

$obErro = new Erro;
$obRMONCredito = new RMONCredito;
$obRARRAvaliacaoImobiliaria = new RARRAvaliacaoImobiliaria;

$obAtributos = new MontaAtributos;
$obAtributos->setName( "Atributo_" );
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {
    case "incluir":
        /*
        //verificar se existe transferencia
        if ($_REQUEST['boItbi'] == 1) {
            require_once ( CAM_GT_CIM_NEGOCIO."RCIMTransferencia.class.php"         );
            $obRCIMTransferencia         = new RCIMTransferencia;
            $obRCIMTransferencia->inInscricaoMunicipal = $_REQUEST['inInscricaoImobiliaria'];
            $obRCIMTransferencia->boEfetivacao = 't';
            $obRCIMTransferencia->listarTransferencia($rsListaTransf);
            if ( $rsListaTransf->getNumLinhas() < 1) {
                $obErro->setDescricao('Nenhuma Transferência cadastrada para o Imóvel '.$_REQUEST['inInscricaoImobiliaria']);
            }
        }
        */

        /* se configurado para informado então cria novo registro na tabela imovel_v_venal */
        $obRARRAvaliacaoImobiliaria->obRCIMImovel->setNumeroInscricao( $_REQUEST['inInscricaoImobiliaria'] );

        $flTmpTerritorial = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flTerritorialInformado'])), 2, '.', '' );
        $flTmpPredial     = number_format( str_replace(',', '.', str_replace('.','',$_REQUEST['flPredialInformado'])), 2, '.', '' );

        if ( !$flTmpTerritorial )
            $flTmpTerritorial = 0.00;
        if ( !$flTmpPredial )
            $flTmpPredial = 0.00;

        $obRARRAvaliacaoImobiliaria->setValorVenalTerritorial( $flTmpTerritorial );
        $obRARRAvaliacaoImobiliaria->setValorVenalPredial( $flTmpPredial );

        $flTmpTotal = $flTmpTerritorial + $flTmpPredial;
        $obRARRAvaliacaoImobiliaria->setInformado( TRUE );
        $obRARRAvaliacaoImobiliaria->setInNumCGM ( $_REQUEST["inNumCGM"]);
        $obRARRAvaliacaoImobiliaria->setExercicio( $_REQUEST['stExercicio'] );
        $obRARRAvaliacaoImobiliaria->setValorVenalTotal( number_format($flTmpTotal, 2, ',', '.') );
//        $obRARRAvaliacaoImobiliaria->setDataVencimento( $_REQUEST['dtDataVencimento'] );

        // dados de atributos dinamicos
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }

         // $obRARRAvaliacaoImobiliaria->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , number_format( str_replace(',', '.', str_replace('.','',$value)), 2, '.', '' ));
           $obRARRAvaliacaoImobiliaria->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo, $value );
        }

        // dados da parcela
        $stParcelas[0] = array (   'stTipoParcela' => '1',
                                    'dtVencimento'  => $_REQUEST['dtDataVencimento']
                                );

        Sessao::write( 'parcelas', $stParcelas );
        if ( !$obErro->ocorreu() ) {
            /*
            if ( $_REQUEST['dtVencimento'] && ($_REQUEST['boItbi'] == 1) ) {
                $obRARRAvaliacaoImobiliaria->obCalculo = new RARRCalculo;
                $obRARRAvaliacaoImobiliaria->obCalculo->obRCIMImovel->inNumeroInscricao = $_REQUEST["inInscricaoImobiliaria"];
                $obRARRAvaliacaoImobiliaria->obCalculo->obRCIMImovel->addProprietario();
                $obRARRAvaliacaoImobiliaria->obCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMInicial = $_REQUEST["inNumCGM"];
                $obRARRAvaliacaoImobiliaria->obCalculo->obRCIMImovel->roUltimoProprietario->inNumeroCGMFinal   = $_REQUEST["inNumCGM"];
                $obRARRAvaliacaoImobiliaria->obCalculo->setParametros($boInformado);
                $obRARRAvaliacaoImobiliaria->obCalculo->setExercicio($_REQUEST['stExercicio']);
                $obRARRAvaliacaoImobiliaria->obCalculo->obRCGM->setNumCGM ( $_REQUEST["inNumCGM"] );
                $obRARRAvaliacaoImobiliaria->obCalculo->obRModulo->setCodModulo( Sessao::read('modulo') );
                $obRARRAvaliacaoImobiliaria->obCalculo->obRMONCredito->setDescricao('I.T.B.I.');
            }
            */

            $obErro = $obRARRAvaliacaoImobiliaria->avaliarImovel();

            if ($_REQUEST['boCarne']) {
                include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php");
                $obRARRCarne = new RARRCarne;
                $obRARRCarne->inInscricaoImobiliariaInicial = $_REQUEST["inInscricaoImobiliaria"];
                $obRARRCarne->setExercicio ( Sessao::getExercicio() );
                $obRARRCarne->obRARRParcela->roRARRLancamento->inCodLancamento = $obRARRAvaliacaoImobiliaria->obCalculo->obRARRLancamento->inCodLancamento;
                include_once 'PREmitirCarne.php';
                exit;
            }
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Imóvel avaliado:  ".$obRARRAvaliacaoImobiliaria->obRCIMImovel->getNumeroInscricao(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}
