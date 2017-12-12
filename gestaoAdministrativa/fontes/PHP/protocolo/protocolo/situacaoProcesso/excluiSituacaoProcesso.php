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
* Arquivo de implementação de situação de processo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3181 $
$Name$
$Author: lizandro $
$Date: 2005-11-30 16:51:23 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.06.98
*/
?>
<?php
    include '../../../framework/include/cabecalho.inc.php';
    include '../situacaoProcesso.class.php';
    $nom = pegaDado("nom_situacao","sw_situacao_processo","Where cod_situacao = '".$codigo."'");
    $exclui = new situacaoProcesso;
    if ($exclui->excluiSituacaoProcesso($codigo)) {
        include '../../classes/auditoria.class.php';
        $audicao = new auditoria;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $codigo); //registra os passos no auditoria
        $audicao->insereAuditoria();
        echo'
                <script type="text/javascript">
                alertaAviso("Situação '.$nom.'","excluir","aviso", "'.Sessao::getId().'");
                mudaTelaPrincipal("listaSituacaoProcesso.php?'.Sessao::getId().'&acao=46");
                </script>';
    } else
        echo'
                <script type="text/javascript">
                alertaAviso("Situação '.$nom.'","n_excluir","erro", "'.Sessao::getId().'");
                mudaTelaPrincipal("listaSituacaoProcesso.php?'.Sessao::getId().'&acao=46");
                </script>';
?>
<?php include("../../includes/rodape.php");?>
