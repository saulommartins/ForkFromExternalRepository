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
    * Página de filtro do relatório
    * Data de Criação   : 16/10/2007


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Vitor Hugo 
    * @ignore
    
    * $Id: OCMontaLivroDivida.php 64409 2016-02-18 11:48:46Z franver $

    * Casos de uso: uc-05.04.10
*/

/*
$Log: ,v $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php" );
include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php"    );
SistemaLegado::BloqueiaFrames(true,true);
    $stFiltro = '';
    if( $_REQUEST['inCodInscricaoInicial']){
        $arCodInscricaoInicial =  explode('/', $_REQUEST['inCodInscricaoInicial'] );
        $inCodInscricaoInicial = $arCodInscricaoInicial[0];
        $inExercicioInicial    = $arCodInscricaoInicial[1]; 
    }
    if( $_REQUEST['inCodInscricaoFinal']){
        $arCodInscricaoFinal =  explode('/', $_REQUEST['inCodInscricaoFinal'] );
        $inCodInscricaoFinal   = $arCodInscricaoFinal[0];
        $inExercicioFinal      = $arCodInscricaoFinal[1]; 
    }
    
    if ( ( $inCodInscricaoInicial != "") and ( $inCodInscricaoFinal != ""  ) )
    {
        $stFiltro = " dda.cod_inscricao between ".$inCodInscricaoInicial." AND  ".$inCodInscricaoFinal." AND ";
    }
    elseif ( ( $inCodInscricaoInicial != "") and ( $inCodInscricaoFinal == ""  ) )
    {
        $stFiltro .= " dda.cod_inscricao = ".$inCodInscricaoInicial." AND ";
    }
    elseif ( ( $inCodInscricaoInicial == "") and ( $inCodInscricaoFinal != ""  ) )
    {
        $stFiltro .= " dda.cod_inscricao = ".$inCodInscricaoFinal." AND ";
    }

   if( $_REQUEST['inLivroFolhaInicial'] ){
       $arTMP = explode( "/", $_REQUEST['inLivroFolhaInicial'] );
       $inLivroInicial      = $arTMP[0];
       $inLivroExercicioInicial = $arTMP[1];
   }
   if( $_REQUEST['inLivroFolhaFinal'] ){
       $arTMP = explode( "/", $_REQUEST['inLivroFolhaFinal'] );
       $inLivroFinal      = $arTMP[0];
       $inLivroExercicioFinal = $arTMP[1];
   }  

$inFolhaInicial = $_REQUEST['inFolhaInicial'];
$inFolhaFinal   = $_REQUEST['inFolhaFinal'];

   if( $inLivroExercicioInicial and $inLivroExercicioFinal ){
        if( $inLivroExercicioInicial == $inLivroExercicioFinal ){
            $stFiltro .= " dda.exercicio_livro = '".$inLivroExercicioInicial."' and "; 
        }else{
            $stFiltro .= " dda.exercicio_livro between '".$inLivroExercicioInicial."' and '".$inLivroExercicioFinal."' and ";
        }
   }else
        if( $inLivroExercicioInicial and !$inLivroExercicioFinal ){
            $stFiltro .= " dda.exercicio_livro = '".$inLivroExercicioInicial."' and ";
        }else
            if( !$inLivroExercicioInicial and $inLivroExercicioFinal ){
                $stFiltro .= " dda.exercicio_livro = '".$inLivroExercicioFinal."' and ";
            }

   if( $inLivroInicial and $inLivroFinal ){
       $stFiltro .= " dda.num_livro between ".$inLivroInicial." AND ".$inLivroFinal." AND "; 
   }elseif( $inLivroInicial and !$inLivroFinal){
       $stFiltro .= " dda.num_livro = ".$inLivroInicial." AND "; 
   }elseif( !$inLivroInicial and $inLivroFinal){
       $stFiltro .= " dda.num_livro = ".$inLivroFinal." AND "; 
   } 
   if( $inFolhaInicial and $inFolhaFinal ){
       $stFiltro .= " dda.num_folha between ".$inFolhaInicial." AND ".$inFolhaFinal." AND "; 
   }elseif( $inFolhaInicial and !$inFolhaFinal ){
       $stFiltro .= " dda.num_folha = ".$inFolhaInicial." AND "; 
   }elseif( !$inFolhaInicial and $inFolhaFinal ){
       $stFiltro .= " dda.num_folha = ".$inFolhaFinal." AND "; 
   }

    if ( $_REQUEST['inCGM'] != "" )
    {
       $stFiltro .= " ddc.numcgm = ".$_REQUEST['inCGM']." AND \n";
    }     
  
    if ( ( $_REQUEST['inCodImovelInicial'] != "" ) AND ( $_REQUEST['inCodImovelFinal'] != "" ) )
    {
        $stFiltro .= " ddi.inscricao_municipal between ".$_REQUEST['inCodImovelInicial']." AND ".$_REQUEST['inCodImovelFinal']." AND \n";
    }
    elseif  ( ( $_REQUEST['inCodImovelInicial'] != "" ) AND ( $_REQUEST['inCodImovelFinal'] == "" ) )
    {
        $stFiltro .= " ddi.inscricao_municipal = ".$_REQUEST['inCodImovelInicial']." AND \n";
    }
    elseif  ( ( $_REQUEST['inCodImovelInicial'] == "" ) AND ( $_REQUEST['inCodImovelFinal'] != "" ) )
    {
        $stFiltro .= " ddi.inscricao_municipal = ".$_REQUEST['inCodImovelFinal']." AND \n";
    }

    //inNumInscricaoEconomicaInicial

    if ( ( $_REQUEST['inNumInscricaoEconomicaInicial'] != "" ) AND ( $_REQUEST['inNumInscricaoEconomicaFinal'] != "" ) )
    {
        $stFiltro .= " dde.inscricao_economica between ".$_REQUEST['inNumInscricaoEconomicaInicial']." AND ".$_REQUEST['inNumInscricaoEconomicaFinal']." AND \n";
    }
    elseif  ( ( $_REQUEST['inNumInscricaoEconomicaInicial'] != "" ) AND ( $_REQUEST['inNumInscricaoEconomicaFinal'] == "" ) )
    {
        $stFiltro .= " dde.inscricao_economica = ".$_REQUEST['inNumInscricaoEconomicaInicial']." AND \n";
    }
    elseif  ( ( $_REQUEST['inNumInscricaoEconomicaInicial'] == "" ) AND ( $_REQUEST['inNumInscricaoEconomicaFinal'] != "" ) )
    {
        $stFiltro .= " dde.inscricao_economica = ".$_REQUEST['inNumInscricaoEconomicaFinal']." AND \n";
    }


    if ( ( $inExercicioInicial != "") and ( $inExercicioFinal != ""  ) )
    {
        $stFiltro .= " dda.exercicio between '".$inExercicioInicial."' AND  '".$inExercicioFinal."' AND \n";
    }
    elseif ( ( $inExercicioInicial != "") and ( $inExercicioFinal == ""  ) )
    {
        $stFiltro .= " dda.exercicio = '".$inExercicioInicial."' AND \n";
    }
    elseif ( ( $inExercicioInicial == "") and ( $inExercicioFinal != ""  ) )
    {
        $stFiltro .= " dda.exercicio  = '".$inExercicioFinal."' AND \n";
    }

    if ($stFiltro) 
    {
        $stFiltro = " WHERE " . substr($stFiltro, 0, -5);
    }

    $stFiltro .= " ) as consulta ";

    $preview = new PreviewBirt(5,33,1);
    $preview->setVersaoBirt('2.5.0');
    $preview->setTitulo('Livro de Dívida Ativa');
    $preview->addParametro( 'stFiltro', $stFiltro );
    $preview->setFormato('pdf');
    $preview->preview();
?>