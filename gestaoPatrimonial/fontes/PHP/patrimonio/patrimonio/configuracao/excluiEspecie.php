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
    * Aqruivo que faz a exclusão das espécies
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 22755 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 23:19:28 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-03.01.05
*/

/*
$Log$
Revision 1.25  2007/05/22 02:18:28  diego
Bug #9275#

Revision 1.24  2007/04/23 16:02:24  rodrigo_sr
Bug #8330#

Revision 1.23  2006/10/11 16:05:40  larocca
Bug #6912#

Revision 1.22  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.21  2006/07/13 20:23:19  fernando
Alteração de hints

Revision 1.20  2006/07/13 14:53:57  fernando
Alteração de hints

Revision 1.19  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.18  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.05");
if (!(isset($ctrl)))
    $ctrl = 0;

if (isset($chave)) {
    $ctrl = 2;
    $pagina = $sessao->transf2;
}
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($ctrl) {
    case 3:
     if ($codNatureza != $codNaturezaAnt) {
            $codGrupo = "";
        }

                    $sSQL = "SELECT * FROM patrimonio.grupo WHERE cod_natureza = ".$codNatureza." ORDER by nom_grupo";

                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $comboGrupo = "";
                $i = 1;
                while (!$dbEmp->eof()) {
                    $codGrupof  = trim($dbEmp->pegaCampo("cod_grupo"));
                    $nomGrupo  = trim($dbEmp->pegaCampo("nom_grupo"));
                    $dbEmp->vaiProximo();                     $comboGrupo .= "parent.window.frames['telaPrincipal'].document.frm.codGrupo.options[".$i."] = new Option ('".addslashes($nomGrupo)."',".$codGrupof.");";
                    if ($codGrupof == $codGrupo) {
                       $comboGrupo .= "parent.window.frames['telaPrincipal'].document.frm.codGrupo.selectedIndex = ".$i.";";
                    }
                    $i++;
                }

                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
                $comboGrupo .= "parent.window.frames['telaPrincipal'].document.frm.codNaturezaAnt.value = ".$codNatureza.";";                $comboGrupo .= "parent.window.frames['telaPrincipal'].document.frm.codTxtGrupo.value = '".$codGrupo."';"
                ?>
                   <script> <?=$comboGrupo?></script>
                <?php

    break;

    // filtra Especies cadastradas por Natureza->Grupo
    case 0:
?>
    <script type="text/javascript">

        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;

            campo = document.frm.codNatureza.value;
            if (campo == 'xxx') {
                mensagem += "@O campo Natureza é obrigatório.";
                erro = true;
            }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);
        }

        function Salvar()
        {
            if (Valida()) {
               document.frm.action = "excluiEspecie.php?<?=Sessao::getId()?>&ctrl=1";
               document.frm.submit();
            }
        }

        // desabilita botao 'OK' se o valor informado no input text nao existir e vice-versa
        // submete o formulario para preencher os campos dependentes ao combo selecionado
        function verificaCombo(campo_a, campo_b)
        {
            var aux;
            aux = preencheCampo(campo_a, campo_b);
            var target = document.frm.target;
            if (aux == false) {
                document.frm.ok.disabled = true;
                document.frm.action = "excluiEspecie.php?<?=Sessao::getId()?>&ctrl=3";
                document.frm.target = 'oculto';
                document.frm.submit();
                document.frm.target = target;
            } else {
                document.frm.ok.disabled = false;
                document.frm.action = "excluiEspecie.php?<?=Sessao::getId()?>&ctrl=3";
                document.frm.target = 'oculto';
                document.frm.submit();
                document.frm.target = target;
            }
        }

       // limpa todo e qualquer quando pré-definido no formulário
       function Limpar()
       {
        var i;
        with(document.forms[0]){
         for (i=0;i<elements.length;i++) {
          if (elements[i].type=="text") {
           elements[i].value=null;
          }
          if (elements[i].type=="select-one") {
           elements[i].options.selectedIndex = 0;
          }
         }
        }
       }
    </script>

    <form name="frm" action="excluiEspecie.php?<?=Sessao::getId()?>" method="POST">

    <table width="100%">

    <tr>
        <td colspan="2" class="alt_dados">Filtrar Espécie</td>
    </tr>

    <tr>
    <td class="label" width="20%" title="Selecione a natureza do bem.">*Natureza</td>
    <td class="field" width="80%">

        <input type="text" name="codTxtNatureza" value="<?=$codNatureza != "xxx" ? $codNatureza : "";?>" size="10" maxlength="10"
            onChange="javascript: verificaCombo(this, document.frm.codNatureza);"
            onKeyPress="return(isValido(this, event, '0123456789'));">

        <select name="codNatureza" onChange="javascript: verificaCombo(this, document.frm.codTxtNatureza); " style="width:200px">
            <option value="xxx">Selecione</option>
<?php
            $sSQL = "SELECT * FROM patrimonio.natureza ORDER by nom_natureza";
            $conn = new dataBaseLegado;
            $conn->abreBD();
            $conn->abreSelecao($sSQL);
            $conn->vaiPrimeiro();
            $comboNatureza = "";

            while (!$conn->eof()) {
                $codNaturezaf  = trim($conn->pegaCampo("cod_natureza"));
                $nomNatureza  = trim($conn->pegaCampo("nom_natureza"));
                $conn->vaiProximo();
                $comboNatureza .= "<option value=".$codNaturezaf;
                        if (isset($codNatureza)) {
                            if ($codNaturezaf == $codNatureza)
                            $comboNatureza .= " SELECTED";
                        }
                $comboNatureza .= ">".$nomNatureza."</option>\n";
            }

            $conn->limpaSelecao();
            $conn->fechaBD();

            echo $comboNatureza;
?>
        </select>
    </td>
    </tr>

<?php
        if ($codNatureza != $codNaturezaAnt) {
            $codGrupo = "";
        }
?>
        <input type="hidden" name="codNaturezaAnt" value="<?=$codNatureza;?>">

    <tr>
        <td class="label" title="Selecione o grupo do bem.">Grupo</td>
        <td class="field">

        <input type="text" name="codTxtGrupo" size="10" maxlength="10" value="<?=$codGrupo != "xxx" ? $codGrupo : "";?>"
            onChange="javascript: verificaCombo(this, document.frm.codGrupo);"
            onKeyPress="return(isValido(this, event, '0123456789'));">

        <select name="codGrupo" onChange="javascript: verificaCombo(this, document.frm.codTxtGrupo);" style="width:200px">
            <option value="xxx" SELECTED>Selecione</option>
<?php
            if (isset($codNatureza)) {

                if ($codNatureza != "xxx") {

                $sSQL = "SELECT * FROM patrimonio.grupo WHERE cod_natureza = ".$codNatureza." ORDER by nom_grupo";
                $conn = new dataBaseLegado;
                $conn->abreBD();
                $conn->abreSelecao($sSQL);
                $conn->vaiPrimeiro();
                $comboGrupo = "";

                while (!$conn->eof()) {
                    $codGrupof  = trim($conn->pegaCampo("cod_grupo"));
                    $nomGrupo  = trim($conn->pegaCampo("nom_grupo"));
                    $conn->vaiProximo();
                    $comboGrupo .= "<option value=".$codGrupof;
                            if (isset($codGrupo)) {
                                if ($codGrupof == $codGrupo)
                                $comboGrupo .= " SELECTED";
                            }
                    $comboGrupo .= ">".$nomGrupo."</option>\n";
                }

                $conn->limpaSelecao();
                $conn->fechaBD();

                echo $comboGrupo;
            }
        }
?>
        </select>

        </td>
    </tr>

    <tr>
        <td class=field colspan=2>
        <?php geraBotaoOk2(); ?>

        </td>
    </tr>

    </table>

    </form>

<?php
    break;

    // listagem das Especies cadastradas utilizando o filtro quando setado
    case 1:

        // monta filtros para a consulta
        $filtro_NatGrp = "";

        // filtro por natureza
        if ($codNatureza and $codNatureza != 'xxx') {
            $filtro_NatGrp .= "AND n.cod_natureza = ".$codNatureza;
        }

        // filtro por grupo
        if ($codGrupo != 'xxx' and $codGrupo) {
            $filtro_NatGrp .= "AND g.cod_grupo = ".$codGrupo;
        }

        $sSQLs = "
            SELECT
                e.cod_especie, e.cod_grupo, e.cod_natureza, e.nom_especie, g.nom_grupo, n.nom_natureza
            FROM
                patrimonio.especie as e, patrimonio.grupo as g, patrimonio.natureza as n
            WHERE
                e.cod_natureza = n.cod_natureza
                AND g.cod_natureza = n.cod_natureza
                AND e.cod_grupo = g.cod_grupo
                $filtro_NatGrp
            ";

         if (!isset($pagina)) {
            $sessao->transf = $sSQLs;
        }

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sessao->transf,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento="&ctrl=1";
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_especie","ASC");
        $sSQLs = $paginacao->geraSQL();

        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sSQLs);

        if ( $pagina > 0 and $conn->eof() ) {
            $pagina--;
            $paginacao->pegaPagina($pagina);
            $paginacao->complemento="&ctrl=1";
            $paginacao->geraLinks();
            $paginacao->pegaOrder("nom_especie","ASC");
            $sSQL = $paginacao->geraSQL();
            $conn->abreSelecao($sSQLs);
        }

        $conn->vaiPrimeiro();

        $sessao->transf2 = $pagina;
?>
        <table width="100%">
        <tr>
            <td class="alt_dados" colspan="8">Registros da Espécie</td>
        </tr>
        <tr>
            <td class="labelcenter" width="5%" rowspan="2">&nbsp;</td>
            <td class="labelcenter" width="30%" colspan="2">Natureza</td>
            <td class="labelcenter" width="30%" colspan="2">Grupo</td>
            <td class="labelcenter" width="30%" colspan="2">Espécie</td>
            <td class="labelcenter" width="1" rowspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td class="labelcenter" width="1%">Código</td>
            <td class="labelcenter">Descrição</td>
            <td class="labelcenter" width="1%">Código</td>
            <td class="labelcenter">Descrição</td>
            <td class="labelcenter" width="1%">Código</td>
            <td class="labelcenter">Descrição</td>
        </tr>
<?php
        $cont = $paginacao->contador();

        while (!$conn->eof()) {
            $codGrupof  = trim($conn->pegaCampo("cod_grupo"));
            $nomGrupof  = trim($conn->pegaCampo("nom_grupo"));
            $codNaturezaf  = trim($conn->pegaCampo("cod_natureza"));
            $nomNaturezaf  = trim($conn->pegaCampo("nom_natureza"));
            $codEspecief  = trim($conn->pegaCampo("cod_especie"));
            $nomEspecief  = trim($conn->pegaCampo("nom_especie"));

            $chave = $codEspecief."-".$codGrupof."-".$codNaturezaf;

            $conn->vaiProximo();
?>
            <tr>
                <td class="labelcenter" width="5%"><?=$cont++;?></td>
                <td class="show_dados_right">&nbsp;<?=$codNaturezaf;?></td>
                <td class="show_dados">&nbsp;<?=$nomNaturezaf;?></td>
                <td class="show_dados_right">&nbsp;<?=$codGrupof;?></td>
                <td class="show_dados">&nbsp;<?=$nomGrupof;?></td>
                <td class="show_dados_right">&nbsp;<?=$codEspecief;?></td>
                <td class="show_dados">&nbsp;<?=$nomEspecief;?></td>
                <td class="botao" title="Excluir" width=1>
                    <a href='#' onClick="alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/configuracao/excluiEspecie.php?<?=Sessao::getId();?>','chave','<?=$chave;?>','Espécie: <?=$nomEspecief;?>','sn_excluir','<?=Sessao::getId();?>')">
                    <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif'  border="0"></a>
                </td>
            </tr>
<?php
        }
?>
        </table>
<?php
        $conn->limpaSelecao();
        $conn->fechaBD();
?>
        <table width="100%" align="center">
        <tr>
            <td align="center"><font size=2>
                <?=$paginacao->mostraLinks();?>
            </font></td>
        </tr>
        </table>
<?php
    break;

    // executa exclusao da especie selecionada no BD
    case 2:
        $variaveis = explode("-",$chave);
        $codEspecie = $variaveis[0];
        $codGrupo = $variaveis[1];
        $codNatureza = $variaveis[2];
        $nomEspecie = pegaDado("nom_especie","patrimonio.especie","where cod_especie = '".$codEspecie."' and cod_grupo = '".$codGrupo."' and cod_natureza = '".$codNatureza."' ");
        $objeto = "Espécie: ".$nomEspecie;

        include_once '../configPatrimonio.class.php';

        $patrimonio = new configPatrimonio;
        $patrimonio->setaVariaveisEspecie($codGrupo, $codNatureza, $codEspecie);

        if ($patrimonio->deleteEspecieAtributos()) {
            if ($patrimonio->deleteEspecie()) {
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $objeto);
            $audicao->insereAuditoria();

            echo '
                <script type="text/javascript">
                    alertaAviso("'.$objeto.'","excluir","aviso","'.Sessao::getId().'");
                    window.location = "excluiEspecie.php?ctrl=1&pagina='.$pagina.'&'.Sessao::getId().'";
                </script>';

            } else {

                echo '
                    <script type="text/javascript">
                        alertaAviso("'.$objeto.'","n_excluir","erro","'.Sessao::getId().'");
                        window.location = "excluiEspecie.php?ctrl=1&pagina='.$pagina.'&'.Sessao::getId().'";
                    </script>';
            }

        } else {
            echo '
                <script type="text/javascript">
                    alertaAviso("'.$objeto.'","n_excluir","erro","'.Sessao::getId().'");
                    window.location = "excluiEspecie.php?ctrl=1&pagina='.$pagina.'&'.Sessao::getId().'";
                </script>';
        }
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
