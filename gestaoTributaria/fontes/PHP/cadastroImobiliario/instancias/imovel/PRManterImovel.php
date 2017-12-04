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
    * Página de processamento para o cadastro de imóvel
    * Data de Criação   : 06/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: PRManterImovel.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.13  2007/02/06 12:31:00  cercato
Bug #6432#

Revision 1.12  2006/09/18 10:30:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO

//Define o nome dos arquivos PHP
$stPrograma = "ManterImovel" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;
$pgFormEdificacao = "../edificacao/FMManterEdificacaoVinculo.php";
include_once( $pgJS );

$obErro = new Erro;

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->roRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );

$obAtributos = new MontaAtributos;
$obAtributos->setName( "Atributo_" );
$obAtributos->recuperaVetor( $arChave );

function alertaAvisoRedirect($location="", $objeto="", $tipo="n_incluir", $chamada="erro", $sessao, $caminho="", $func="")
{
    ;
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                alertaAviso      ( "'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
           </script>';
//    session_regenerate_id();
  //  Sessao::setId("PHPSESSID=".session_id());
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
//    $sessao->geraURLRandomica();
    Sessao::write('acaoLote'  , "751");
    Sessao::write('modulo',  "12");
    print '<script type="text/javascript">
                mudaMenu         ( "'.$func.'"     );
                mudaTelaPrincipal( "'.$location.'" );
           </script>';
}

switch ($stAcao) {
    case "incluir":
        //VERIFICACAO DA DATA DE INSCRICAO SER MAIOR QUE A DATA DE INCLUSAO DO LOTE
        $obRCIMImovel->roRCIMLote->consultarLote();
        if ( sistemaLegado::comparaDatas($obRCIMImovel->roRCIMLote->getDataInscricao(),$_REQUEST['dtDataInscricaoImovel']) ) {
            $obErro->setDescricao('Data inscrição do imóvel ('.$_REQUEST['dtDataInscricaoImovel'].') inferior a data de inscrição do lote ('.$obRCIMImovel->roRCIMLote->getDataInscricao().')');
        }
        if ( !$obErro->ocorreu() ) {
            $obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
            $obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );

            //ABA INSCRICAO IMOBILIARIA
            $obRCIMImovel->setNumeroInscricao          ( $_REQUEST["inNumeroInscricao"]           );
            $obRCIMImovel->setMatriculaRegistroImoveis ( $_REQUEST["stMatriculaRegistroImoveis"]  );
            $obRCIMImovel->setZona                     ( $_REQUEST["stMatriculaZona"]             );
            $obRCIMImovel->setDataInscricao            ( $_REQUEST["dtDataInscricaoImovel"]       );
            $obRCIMImovel->setNumeroImovel             ( (int) $_REQUEST["stNumeroImovel"]         );
            $obRCIMImovel->setCepImovel                ( $_REQUEST["inCEP"]                       );
            $obRCIMImovel->setComplementoImovel        ( $_REQUEST["stComplementoImovel"]         );
            $obRCIMImovel->obRCIMCondominio->setCodigoCondominio( $_REQUEST["inCodigoCondominio"] );
            $obRCIMImovel->obRCIMImobiliaria->setRegistroCreci( $_REQUEST["stCreciResponsavel"]   );
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"]  );
            $obRCIMImovel->obRProcesso->setCodigoProcesso( $arProcesso[0] );
            $obRCIMImovel->obRProcesso->setExercicio     ( $arProcesso[1] );
            $obRCIMImovel->obRCIMConfrontacaoTrecho->setCodigoConfrontacao( $_REQUEST["inCodigoConfrontacao"] );
            //SETANDO PROPRIETARIOS E PROP PROMITENTES
            $inOrdem = 1;
            if (is_array( Sessao::read('proprietarios'))) {
            foreach ( Sessao::read('proprietarios') as $inChave => $arProprietario ) {
                $obRCIMImovel->addProprietario();
                $obRCIMImovel->roUltimoProprietario->setNumeroCGM  ( $arProprietario["inNumCGM"] );
                $obRCIMImovel->roUltimoProprietario->setOrdem      ( $inOrdem++ );
                $obRCIMImovel->roUltimoProprietario->setPromitente ( "f" );
                $obRCIMImovel->roUltimoProprietario->setCota       ( $arProprietario["flQuota"] );
            }}

            if ( is_array( Sessao::read('promitentes'))) {
            foreach ( Sessao::read('promitentes') as $inChave => $arPromitente ) {
                $obRCIMImovel->addProprietarioPromitente();
                $obRCIMImovel->roUltimoProprietarioPromitente->setNumeroCGM  ( $arPromitente["inNumCGM"] );
                $obRCIMImovel->roUltimoProprietarioPromitente->setOrdem      ( $inOrdem++ );
                $obRCIMImovel->roUltimoProprietarioPromitente->setPromitente ( "t" );
                $obRCIMImovel->roUltimoProprietarioPromitente->setCota       ( $arPromitente["flQuota"] );
            }}

            //ENDERECO DE ENTREGA
            if ($_REQUEST["boEnderecoEntrega"]) {
                 $obRCIMImovel->addImovelCorrespondencia();
                 $obRCIMImovel->setCEPEntrega( str_replace( "-", "", $_REQUEST["cbCep"] ) );
                 $obRCIMImovel->setNumeroEntrega( $_REQUEST["stNumero"] );
                 $obRCIMImovel->setCodigoBairroEntrega( $_REQUEST["codBairro"] );
                 $obRCIMImovel->setCaixaPostal( $_REQUEST["stCaixaPostal"] );
                 $obRCIMImovel->setComplementoEntrega( $_REQUEST["stComplemento"] );
                 $obRCIMImovel->obRCIMLogradouroEntrega->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] );
            }

            //ATRIBUTOS IMOVEL
            foreach ($arChave as $key=>$value) {
                 $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                 $inCodAtributo = $arChaves[0];
                 if ( is_array($value) ) {
                     $value = implode(",",$value);
                 }
                 $obRCIMImovel->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
        }

            $obFlagTransacao = false;

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCIMImovel->incluirImovel();
        }
        if ( !$obErro->ocorreu() ) {
           if ($_REQUEST["boSeguir"]) {
                alertaAvisoRedirect($pgFormEdificacao."?stAcao=incluir&boVinculoEdificacao=Imóvel&boAdicionarEdificacao=Não&inInscImovel=".$obRCIMImovel->getNumeroInscricao(),"Número da inscrição: ".$obRCIMImovel->getNumeroInscricao(),"incluir","aviso",Sessao::getId(),"../","183" );
           } else {
                SistemaLegado::alertaAviso($pgForm,"Número da inscrição: ".$obRCIMImovel->getNumeroInscricao(),"incluir","aviso", Sessao::getId(), "../");
           }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obRCIMImovel->roRCIMLote->consultarLote();

        if ( sistemaLegado::comparaDatas($obRCIMImovel->roRCIMLote->getDataInscricao(),$_REQUEST['dtDataInscricaoImovel']) ) {
            $obErro->setDescricao('Data inscrição do imóvel ('.$_REQUEST['dtDataInscricaoImovel'].') inferior a data de inscrição do lote ('.$obRCIMImovel->roRCIMLote->getDataInscricao().')');
        }

        if ( !$obErro->ocorreu() ) {
            //ABA INSCRICAO IMOBILIARIA
            $obRCIMImovel->setNumeroInscricao          ((int) $request->get("inNumeroInscricao")       );
            $obRCIMImovel->setMatriculaRegistroImoveis ( $request->get("stMatriculaRegistroImoveis")  );
            $obRCIMImovel->setZona                     ( $request->get("stMatriculaZona")             );
            $obRCIMImovel->setCodigoSubLote            ( $request->get("inCodigoSubLote")             );
            $obRCIMImovel->setDataInscricao            ( $request->get("dtDataInscricaoImovel")       );
            $obRCIMImovel->setNumeroImovel             ( (int) $request->get("stNumeroImovel")         );
            $obRCIMImovel->setCepImovel                ( $request->get("inCEP")                       );
            $obRCIMImovel->setComplementoImovel        ( $request->get("stComplementoImovel")        );
            $obRCIMImovel->obRCIMCondominio->setCodigoCondominio( $request->get("inCodigoCondominio") );
            $obRCIMImovel->obRCIMImobiliaria->setRegistroCreci  ( $request->get("stCreciResponsavel") );
            $obRCIMImovel->obRCIMConfrontacaoTrecho->setCodigoConfrontacao( $request->get("inCodigoConfrontacao") );

            /*
            * Alterado em 12/04/2005 por Lucas Stephanou
            *   Processo passa a ser opcional
            *   Se o processo vir setado na alteração,ele deve ter o timestamp igualado com
            *   o timestamp do imovel.
            */

            if ($request->get("inProcesso")) {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $request->get("inProcesso")    );
            $obRCIMImovel->obRProcesso->setCodigoProcesso( $arProcesso[0]                   );
            $obRCIMImovel->obRProcesso->setExercicio     ( $arProcesso[1]                   );
            $obRCIMImovel->setTimestampImovel            ( $request->get( "hdnTimestampImovel" ));
            $obRCIMImovel->obRCIMConfrontacaoTrecho->setCodigoConfrontacao( $request->get("inCodigoConfrontacao") );
            }

            //SETANDO PROPRIETARIOS E PROP PROMITENTES
            $inOrdem = 1;
            foreach ( Sessao::read('proprietarios') as $inChave => $arProprietario ) {
                $obRCIMImovel->addProprietario();
                $obRCIMImovel->roUltimoProprietario->setNumeroCGM  ( $arProprietario["inNumCGM"] );
                $obRCIMImovel->roUltimoProprietario->setOrdem      ( $inOrdem++ );
                $obRCIMImovel->roUltimoProprietario->setPromitente ( "f" );
                $obRCIMImovel->roUltimoProprietario->setCota       ( $arProprietario["flQuota"] );
            }

            foreach ( Sessao::read('promitentes') as $inChave => $arPromitente ) {
                $obRCIMImovel->addProprietarioPromitente();
                $obRCIMImovel->roUltimoProprietarioPromitente->setNumeroCGM  ( $arPromitente["inNumCGM"] );
                $obRCIMImovel->roUltimoProprietarioPromitente->setOrdem      ( $inOrdem++ );
                $obRCIMImovel->roUltimoProprietarioPromitente->setPromitente ( "t" );
                $obRCIMImovel->roUltimoProprietarioPromitente->setCota       ( $arPromitente["flQuota"] );
            }

            //ENDERECO DE ENTREGA
            if ( $request->get("boEnderecoEntrega") ) {
                 $obRCIMImovel->addImovelCorrespondencia();
                 $obRCIMImovel->setCEPEntrega( str_replace( "-", "", $request->get("cbCep") ) );
                 $obRCIMImovel->setNumeroEntrega( $request->get("stNumero") );
                 $obRCIMImovel->setCodigoBairroEntrega( $request->get("codBairro") );
                 $obRCIMImovel->setCaixaPostal( $request->get("stCaixaPostal") );
                 $obRCIMImovel->setComplementoEntrega( $request->get("stComplemento") );
                 $obRCIMImovel->obRCIMLogradouroEntrega->setCodigoLogradouro( $request->get("inNumLogradouro") );
            }

            //ATRIBUTOS IMOVEL
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];

                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRCIMImovel->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCIMImovel->alterarImovel();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Número da inscrição: ".$obRCIMImovel->getNumeroInscricao(),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
       $obRCIMImovel->setNumeroInscricao ( $_REQUEST["inInscricaoMunicipal"] );
       $obErro = $obRCIMImovel->excluirImovel();
       if ( !$obErro->ocorreu() ) {
           SistemaLegado::alertaAviso($pgList,"Número da inscrição: ".$obRCIMImovel->getNumeroInscricao(),"excluir","aviso", Sessao::getId(), "../");
       } else {
           SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
       }
        break;

    case "reativar":
        $obRCIMImovel->setDataBaixa       ( $_REQUEST["stTimestamp"] );
        $obRCIMImovel->setNumeroInscricao ( $_REQUEST["inNumeroInscricao"] );
        $obRCIMImovel->setJustificativa   ( $_REQUEST["stJustificativa"] );
        $obRCIMImovel->setJustificativaReativar   ( $_REQUEST["stJustReat"] );
        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"]    );
            $obRCIMImovel->obRProcesso->setCodigoProcesso( $arProcesso[0]                   );
            $obRCIMImovel->obRProcesso->setExercicio     ( $arProcesso[1]                   );
        }
        $obErro = $obRCIMImovel->reativarImovel();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Reativar imóvel concluído com sucesso! (Número de inscrição: ".$obRCIMImovel->getNumeroInscricao().")","cc","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso("Erro ao reativar imóvel (".urlencode($obErro->getDescricao()).")","cc","erro");
        }
        break;

    case "baixar":
        $obRCIMImovel->setNumeroInscricao ( $_REQUEST["inNumeroInscricao"] );
        $obRCIMImovel->setJustificativa   ( $_REQUEST["stJustificativa"] );
        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"]    );
            $obRCIMImovel->obRProcesso->setCodigoProcesso( $arProcesso[0]                   );
            $obRCIMImovel->obRProcesso->setExercicio     ( $arProcesso[1]                   );
        }
        $obErro = $obRCIMImovel->baixarImovel();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Número de inscrição: ".$obRCIMImovel->getNumeroInscricao(),"baixar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
        break;

    case "historico":
        $obRCIMImovel->setNumeroInscricao ( $_REQUEST["inNumeroInscricao"] );

        $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"]    );
        $obRCIMImovel->obRProcesso->setCodigoProcesso( $arProcesso[0] );
        $obRCIMImovel->obRProcesso->setExercicio     ( $arProcesso[1] );

        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMImovel->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRCIMImovel->alterarCaracteristicas();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Número de inscrição: ".$obRCIMImovel->getNumeroInscricao() ,"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

}

?>
