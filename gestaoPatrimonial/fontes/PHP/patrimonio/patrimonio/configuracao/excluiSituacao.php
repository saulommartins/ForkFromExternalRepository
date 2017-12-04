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
    * Arquivo que lista as situações a serem excluidas
    * Data de Criação   : 25/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 16683 $
    $Name$
    $Autor: $
    $Date: 2006-10-11 13:08:56 -0300 (Qua, 11 Out 2006) $

    * Casos de uso: uc-03.01.10
*/

/*
$Log$
Revision 1.11  2006/10/11 16:08:56  larocca
Bug #6914#

Revision 1.10  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.9  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:11:28  diego

*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once '../situacao.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.10");

    $lista = new situacao;
    $exclui = new situacao;

    if (!(isset($ctrl)))
        $ctrl = 0;
    if (isset($key)) {
        $ctrl=1;
        $pagina = $sessao->transf2;
    }
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

    switch ($ctrl) {

        case 0:

            $codkey = $mostra->codigo;
 ?>
            <table width=100%>
            <tr>
                <td colspan=4 class=alt_dados>Registros de Situação do Bem</td>
            </tr>
            <tr>
                <td class="labelcenter" width="5%">&nbsp;</td>
                <td class="labelcenter" width="12%">Código</td>
                <td class="labelcenter" width="80%">Descrição da Situação</td>
                <td class="labelcenter">&nbsp;</td>
            </tr>
<?php
            // consulta situacoes de bens...
            $sSQLs = "select cod_situacao, nom_situacao from patrimonio.situacao_bem";

            $sessao->transf = $sSQLs;

            // gera lista de apolices com paginacao
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados($sessao->transf,"10");
            $paginacao->pegaPagina($pagina);
            $paginacao->geraLinks();
            $paginacao->pegaOrder("nom_situacao","ASC");
            $sSQL = $paginacao->geraSQL();

            //Pega os dados encontrados em uma query
            $conn = new dataBaseLegado;
            $conn->abreBD();
            $conn->abreSelecao($sSQL);

            if ( $pagina > 0 and $conn->eof() ) {
                $pagina--;
                $paginacao->pegaPagina($pagina);
                $paginacao->geraLinks();
                $paginacao->pegaOrder("c.nom_cgm, a.num_apolice","ASC");
                $sSQL = $paginacao->geraSQL();
                $conn->abreSelecao($sSQL);
            }

            $conn->vaiPrimeiro();
            $cont = $paginacao->contador();

            $sessao->transf2 = $pagina;

            // lista apolices encontrados
            while (!$conn->eof()) {

                $cod_situacao  = trim($conn->pegaCampo("cod_situacao"));
                $nom_situacao  = trim($conn->pegaCampo("nom_situacao"));
                $nom_situacao2 = AddSlashes($nom_situacao);

                $conn->vaiProximo();
Sessao::getExercicio()
?>
                <tr>
                    <td class="labelcenter" width="5%"><?=$cont++;?></td>
                    <td class="show_dados_right" width="12%"><?=$cod_situacao;?></td>
                    <td class="show_dados" width="80%"><?=$nom_situacao;?></td>
                    <td class="botao" title="Excluir">
                        <a href='' onClick="alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/configuracao/excluiSituacao.php?<?=Sessao::getId();?>','key','<?=$cod_situacao;?>','Situação: <?=$nom_situacao;?>','sn_excluir','<?=Sessao::getId();?>');">
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif'  border='0'></a>
                    </td>

                </tr>
<?php
            }

            $conn->limpaSelecao();
            $conn->fechaBD();
?>
            </table>

            <table width="100%" align="center">
            <tr>
                <td align="center">
                    <font size="2"><?=$paginacao->mostraLinks();?></font>
                </td>
            </tr>
            </table>
<?php
        break;

        // exclui Situacao de Bem do BD
        case 1:

            $exclui->codigo = $key;
            $exclui->nome = pegaDado("nom_situacao","patrimonio.situacao_bem","where cod_situacao = $key");

            $objeto = "Esta Situação está sendo utilizada. Situação: ".$exclui->nome;

            // executa query para delecao da Situacao de Bem do Banco
            if ($exclui->excluiSituacao()) {

                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';

                $audicao = new auditoriaLegada;;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $exclui->nome);
                $audicao->insereAuditoria();

                echo '
                    <script type="text/javascript">
                    alertaAviso("Situação: '.$exclui->nome.'","excluir","aviso","'.Sessao::getId().'");
                    mudaTelaPrincipal("excluiSituacao.php?'.Sessao::getId().'&pagina='.$pagina.'&ctrl=0");
                    </script>';

            // se operacao de deleção nao foi realizada com sucesso exibe msg de erro
            } else {
                echo '
                    <script type="text/javascript">
                    alertaAviso("'.$objeto.'","n_excluir","erro","'.Sessao::getId().'");
                    mudaTelaPrincipal("excluiSituacao.php?'.Sessao::getId().'&ctrl=0");
                    </script>';
            }
    }
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
