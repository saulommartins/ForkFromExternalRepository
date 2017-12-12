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
    * Arquivo que lista as situações dos bens para serem alteradas
    * Data de Criação   : 25/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.10
*/

/*
$Log$
Revision 1.13  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.12  2006/07/13 14:53:57  fernando
Alteração de hints

Revision 1.11  2006/07/06 17:10:05  gelson
Retirado o '*' ao lado da Descrição da Situação na lista.

Revision 1.10  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.9  2006/07/06 12:11:28  diego

*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once '../situacao.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.10");

    $lista = new situacao;
    $altera = new situacao;

    if (!(isset($ctrl)))
        $ctrl = 0;

    switch ($ctrl) {

        // exibe Situacoes de Bens cadastradas
        case 0:

        $lista->codigo = $codigo;

?>
        <table width="100%">
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

        // lista apolices encontrados
        while (!$conn->eof()) {

            $cod_situacao  = trim($conn->pegaCampo("cod_situacao"));
            $nom_situacao  = trim($conn->pegaCampo("nom_situacao"));

            $conn->vaiProximo();
?>
            <tr>
                <td class="labelcenter" width="5%"><?=$cont++;?></td>
                <td class="show_dados_right" width="12%"><?=$cod_situacao;?></td>
                <td class="show_dados" width="80%"><?=$nom_situacao;?></td>
                <td class="botao" title="Alterar">
                    <a href='alteraSituacao.php?<?=Sessao::getId();?>"&cod=<?=$cod_situacao;?>&ctrl=1&pagina=<?=$pagina;?>'>
                    <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif'  border='0'></a>
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

        // abre formulario para alteracao de Situacao de Bem
        case 1:

        $altera->mostraSituacao($cod);
?>
        <script type="text/javascript">

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campoaux;
/*
                campo = document.frm.nom.value.length;
                    if (campo == 0) {
                    mensagem += "@O campo Descrição da Situação é obrigatório.";
                    erro = true;
                }
*/
                campo = trim(document.frm.nom.value).length;
                 if (campo == 0) {
                  mensagem += "@O campo Descrição da Espécie é obrigatório.";
                  erro = true;
                 }
                    if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                    return !(erro);
            }

            function Salvar()
            {
                if (Valida()) {
                    document.frm.submit();
                }
            }

            function Cancela()
            {
                mudaTelaPrincipal("alteraSituacao.php?<?=Sessao::getId();?>&ctrl=0&pagina=<?=$pagina;?>");
            }

        </script>

        <form action="alteraSituacao.php?<?=Sessao::getId()?>&ctrl=2" method="POST" name="frm">

        <table width="100%">

        <tr>
            <td class=alt_dados colspan=2>Dados para a Situação do Bem</td>
        </tr>

        <tr>
            <td class=label>Código</td>
            <td class=field>
                <?=$cod;?>
            </td>
            <input type="hidden" name="cod" value="<?=$altera->codigo;?>">
            <input type="hidden" name="pagina" value="<?=$pagina;?>">
        </tr>

        <tr>
            <td class=label title="Informe a descrição da Situação.">*Descrição da Situação</td>
            <td class=field>
                <input type="text" name="nom" value="<?=$altera->nome;?>" maxlength=60 size=80>
            </td>
        </tr>

        <tr>
            <td class=field colspan=2>
                <?=geraBotaoAltera();?>
            </td>
        </tr>

        </table>

        </form>
<?php
        break;

        // atualiza Situacao de Bem no BD
        case 2:

        $objeto = "Situação: ".$nom;
        $altera->setaVariaveis($cod, $nom);

        // verifica se existe situacao de bem com o nome informado...
        if (comparaValor("nom_situacao", $nom,"patrimonio.situacao_bem", "and cod_situacao <> $cod",1)) {

            // altera dados da situacao de bem
            if ($altera->alteraSituacao()) {
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';

                $audicao = new auditoriaLegada;;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
                $audicao->insereAuditoria();

                echo '
                <script type="text/javascript">
                    alertaAviso("'.$objeto.'","alterar","aviso","'.Sessao::getId().'");
                    mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'&pagina='.$pagina.'");
                </script>';

            // se ocorreu erro na atualizao da situacao de bem,
            // exibe msg de rro
            } else {
                echo '
                <script type="text/javascript">
                alertaAviso("'.$objeto.'","n_alterar","erro","'.Sessao::getId().'");
                mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
                </script>';
            }

        // se ja existe uma situacao de bem com o nome informado,
        // nao atualiza a situcao de bem e exibe msg de erro
        } else {
            echo '
            <script type="text/javascript">
            alertaAviso("A Situação '.$nom.' já existe","unica","erro","'.Sessao::getId().'");
            mudaTelaPrincipal("alteraSituacao.php?'.Sessao::getId().'&nom='.$nom.'&cod='.$cod.'&ctrl=1");
            </script>';
        }
        break;
    }

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
