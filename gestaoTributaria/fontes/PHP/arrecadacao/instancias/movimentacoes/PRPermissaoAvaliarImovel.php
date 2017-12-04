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
  * Página de Processamento Permissao Avaliar Imóvel
  * Data de criação : 20/04/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @ignore

    * $Id: PRPermissaoAvaliarImovel.php 63839 2015-10-22 18:08:07Z franver $

  Caso de uso: uc-05.03.06
*/
/*
$Log$
Revision 1.3  2007/03/16 18:51:25  rodrigo
Bug #8425#

Revision 1.2  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoImobiliaria.class.php"                                );
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php"                                                );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( 'link' );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "PermissaoAvaliarImovel";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

switch ($stAcao) {
    case "incluir":
       $obErro = new Erro;
       $arSessao_tranf5_cgm = array();
       $arSessao_cgm = array();
       Sessao::write( 'Sessao_tranf5_cgm', array() );
       Sessao::write( 'Sessao_cgm', array() );
       $arUsuarios = Sessao::read( 'usuarios' );

       if ( count( $arUsuarios ) >= 1 ) {
           $obRUsuario                 = new RUsuario();
           $rsUsuario                  = new RecordSet();
           $obRARRAvaliacaoImobiliaria = new RARRAvaliacaoImobiliaria();
           $inNro           = 0;
           foreach ($arUsuarios as $key) {
                 $obRUsuario->obRCGM->setNumCGM ( $key["inNumCGM"] );
                 $obRUsuario->consultar( $rsUsuario );
                 if ($rsUsuario->getNumLinhas()>=1) {
                    $arSessao_tranf5_cgm[$inNro]['inNumCGM'] = $key[ "inNumCGM" ];
                    $arSessao_tranf5_cgm[$inNro]['stNomCGM'] = $obRUsuario->getUsername();
                    $inNro++;
                 }
           }

           Sessao::write( 'Sessao_tranf5_cgm', $arSessao_tranf5_cgm );
           $rsVenal = new RecordSet();
           $x = 0;
           foreach ($arSessao_tranf5_cgm as $key) {
               $obRARRAvaliacaoImobiliaria->obTARRPermissaoValorVenal->setDado("numcgm",$key['inNumCGM']);
               $obRARRAvaliacaoImobiliaria->obTARRPermissaoValorVenal->recuperaPorChave( $rsVenal );
                if ( $rsVenal->eof() ) {
//                     unset($sessao->transf5['cgm'][$x]);
                    $arSessao_cgm[$x]['inNumCGM'] = $key['inNumCGM'];
                    $x++;
                }
           }

           Sessao::write( 'Sessao_cgm', $arSessao_cgm );
           if (count( $arSessao_tranf5_cgm ) >= 1 ) {
               $obRARRAvaliacaoImobiliaria->setArNumCGM( $arSessao_cgm );
               $obErro = $obRARRAvaliacaoImobiliaria->IncluirPermissaoUsuario();
               if ( !$obErro->ocorreu() ) {
                   SistemaLegado::alertaAviso($pgForm,"Permissão para usuário","incluir","aviso", Sessao::getId(), "../");
               } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
               }
          }
       } else {
            $obErro->setDescricao( "Não existem usuários na lista." );
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
       }
  break;
}
