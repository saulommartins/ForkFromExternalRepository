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
    * Altera uma Espécie do Patrimônio
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 28299 $
    $Name$
    $Autor: $
    $Date: 2008-02-29 14:31:59 -0300 (Sex, 29 Fev 2008) $

    * Casos de uso: uc-03.01.05
*/

/*
$Log$
Revision 1.26  2007/05/22 02:18:28  diego
Bug #9275#

Revision 1.25  2006/07/21 11:35:56  fernando
Inclusão do  Ajuda.

Revision 1.24  2006/07/13 20:23:19  fernando
Alteração de hints

Revision 1.23  2006/07/13 14:53:57  fernando
Alteração de hints

Revision 1.22  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.21  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.05");
$anoE = Sessao::getExercicio();

if (!(isset($ctrl)))
    $ctrl = 0;

switch ($ctrl) {
   case 4:
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

    //formulario para insercao da Especie com filtro por Natureza->Grupo
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
                document.frm.action = "alteraEspecie.php?<?=Sessao::getId()?>&ctrl=1";
                document.frm.submit();
            }
        }

        function Limpar()
        {
            document.frm.codNatureza.options[0] = new Option('Selecione','', 'selected');
            document.frm.codTxtNatureza.value = "";
            document.frm.codGrupo.options[0] = new Option('Selecione','', 'selected');
            document.frm.codTxtGrupo.value = "";

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
                document.frm.action = "alteraEspecie.php?<?=Sessao::getId()?>&ctrl=4";
                document.frm.target = 'oculto';
                document.frm.submit();
                document.frm.target = target;
            } else {
                document.frm.ok.disabled = false;
                document.frm.action = "alteraEspecie.php?<?=Sessao::getId()?>&ctrl=4";
                document.frm.target = 'oculto';
                document.frm.submit();
                document.frm.target = target;
            }
        }

    </script>

    <form name="frm" action="alteraEspecie.php?<?=Sessao::getId()?>" method="POST">

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

                $sSQL = "SELECT * FROM patrimonio.grupo
                        WHERE
                            cod_natureza = ".$codNatureza." ORDER by nom_grupo";
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
            <td class="labelcenter" width="5%" rowspan="2">&nbsp;</td>
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
                <td class="botao" title="Alterar">
                    <a href='alteraEspecie.php?<?=Sessao::getId();?>&codEspecie=<?=$codEspecief;?>&codNatureza=<?=$codNaturezaf;?>&codGrupo=<?=$codGrupof;?>&ctrl=2&pagina=<?=$pagina;?>'>
                    <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif'  border="0">
                    </a>
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

    // formulario para alteracao de Especie
    case 2:

        // busca dados da Especie selecionada
        $sSQL = "
                SELECT
                    e.cod_especie, e.cod_grupo, e.cod_natureza, e.nom_especie, g.nom_grupo, n.nom_natureza
                FROM
                    patrimonio.especie as e, patrimonio.grupo as g, patrimonio.natureza as n
                WHERE
                    e.cod_natureza = n.cod_natureza
                    AND g.cod_natureza = n.cod_natureza
                    AND e.cod_grupo = g.cod_grupo
                    AND e.cod_natureza = ".$codNatureza."
                    AND e.cod_grupo = ".$codGrupo."
                    AND e.cod_especie = ".$codEspecie;

        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sSQL);
        $conn->vaiPrimeiro();

        $codNatureza = trim($conn->pegaCampo("cod_natureza"));
        $nomNatureza  = trim($conn->pegaCampo("nom_natureza"));
        $nomGrupo  = trim($conn->pegaCampo("nom_grupo"));
        $codGrupo = trim($conn->pegaCampo("cod_grupo"));
        $codEspecie  = trim($conn->pegaCampo("cod_especie"));
        $nomEspecie  = trim($conn->pegaCampo("nom_especie"));

        $conn->limpaSelecao();
        $conn->fechaBD();

        // busca atributos relacionados a esta Especie
        $sSQL = "SELECT
                    cod_atributo
                FROM
                    patrimonio.especie_atributo
                WHERE
                    cod_especie = ".$codEspecie."
                    AND cod_grupo = ".$codGrupo."
                    AND cod_natureza = ".$codNatureza;

        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sSQL);
        $conn->vaiPrimeiro();

        while (!$conn->eof()) {
            $codAtributo = trim($conn->pegaCampo("cod_atributo"));
            $conn->vaiProximo();
            $listaAtributo[$codAtributo] = " checked";
        }

        $conn->limpaSelecao();
        $conn->fechaBD();

?>
        <script type="text/javascript">

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campoaux;

                campo = trim(document.frm.nomEspecie.value).length;
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

            function Limpar()
            {
                document.frm.codNatureza.options[0] = new Option('Selecione','', 'selected');
                document.frm.codTxtNatureza.value = "";
                document.frm.codGrupo.options[0] = new Option('Selecione','', 'selected');
                document.frm.codTxtGrupo.value = "";

            }

            function Cancela()
            {
                mudaTelaPrincipal("alteraEspecie.php?<?=Sessao::getId();?>&ctrl=1&pagina=<?=$pagina;?>");
            }
        </script>

        <form name="frm" action="alteraEspecie.php?<?=Sessao::getId()?>" method="POST">

            <input type="hidden" name="codNatureza" value="<?=$codNatureza;?>">
            <input type="hidden" name="codGrupo" value="<?=$codGrupo;?>">
            <input type="hidden" name="codEspecie" value="<?=$codEspecie;?>">
            <input type="hidden" name="ctrl" value="3">
            <input type="hidden" name="pagina" value="<?=$pagina;?>">

        <table width="100%">
        <tr>
            <td colspan="2" class="alt_dados">Dados da Espécie</td>
        </tr>

        <tr>
            <td class="label" width="20%">Natureza</td>
            <td class="field"><?=$codNatureza;?> - <?=$nomNatureza;?></td>
        </tr>

        <tr>
            <td class="label">Grupo</td>
            <td class=field><?=$codGrupo;?> - <?=$nomGrupo;?></td>
        </tr>

        <tr>
            <td class="label">Código</td>
            <td class="field"><?=$codEspecie;?></td>
        </tr>

        <tr>
            <td class="label" title="Informe a descrição da espécie do bem.">*Descrição da Espécie</td>
            <td class="field">
                <input type="text" name="nomEspecie" size="80" maxlength="80" value="<?=$nomEspecie;?>">
            </td>
        </tr>

        <tr>
            <td class="label" title="Selecione os atributos.">Atributos</td>
            <td class="field">
<?php
                $sSQL = "SELECT * FROM administracao.atributo_dinamico ORDER by nom_atributo";
                $conn = new dataBaseLegado;
                $conn->abreBD();
                $conn->abreSelecao($sSQL);
                $conn->vaiPrimeiro();
                $atributos = "";

                while (!$conn->eof()) {
                    $disabled = "";
                    $cod_atributo  = trim($conn->pegaCampo("cod_atributo"));
                    $nom_atributo  = trim($conn->pegaCampo("nom_atributo"));
                    $conn->vaiProximo();

                    $sSQL2 = "SELECT
                                count(*) as verifica
                            FROM
                                patrimonio.bem_atributo_especie
                            WHERE
                                cod_atributo = ".$cod_atributo."
                                AND cod_especie = ".$codEspecie."
                                AND cod_natureza = ".$codNatureza."
                                AND cod_grupo = ".$codGrupo;

                    $conn2 = new dataBaseLegado;
                    $conn2->abreBD();
                    $conn2->abreSelecao($sSQL2);
                    $conn2->vaiPrimeiro();

                    $verifica = trim($conn2->pegaCampo("verifica"));

                    $conn2->limpaSelecao();
                    //$conn2->fechaBD();

                    // se o atributo selecionado estiver relacionado a algum bem
                    // desabilita o checked para que se valor nao seja alterado
                    if ($verifica != 0) {
                        $disabled = "disabled";
                    } else {
                        $disabled = "";
                    }

                    $atributos .= "<input $disabled type=checkbox name='atributo[]' value=".$cod_atributo;
                    $atributos .= $listaAtributo[$cod_atributo];
                    $atributos .= "> ".$nom_atributo."<br>";
                }

                $conn->limpaSelecao();
                $conn->fechaBD();

                echo $atributos;
?>
            </td>
        </tr>

        <tr>
            <td class="field" colspan="2">
                <?=geraBotaoAltera();?>
            </td>
        </tr>

        </table>

        </form>

<?php
    break;

    // executa alteracao da Especie selecionada no BD
    case 3:

        $msg = "";

        include_once '../configPatrimonio.class.php';

        $patrimonio = new configPatrimonio;

        // alteração de Atributos
        // verifica se algum atributo desta especie esta sendo utlizado por algum bem
        $sSQL = "SELECT
                     count(*) as verifica
                FROM
                    patrimonio.bem_atributo_especie
                WHERE
                    cod_especie = ".$codEspecie."
                    AND cod_natureza = ".$codNatureza."
                    AND cod_grupo = ".$codGrupo;
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sSQL);
        $conn->vaiPrimeiro();
        $verifica = trim($conn->pegaCampo("verifica"));
        $conn->limpaSelecao();
        $conn->fechaBD();

        // verifica a existência
        if ($verifica == 0) {

            $sQuery = "DELETE FROM patrimonio.especie_atributo WHERE cod_especie = '".$codEspecie."' AND cod_natureza = '".$codNatureza."' AND cod_grupo = '".$codGrupo."'";

            while (list($chave,$valor) = each($atributo)) {
                $sQuery .= " ; INSERT INTO patrimonio.especie_atributo (cod_atributo,cod_especie,cod_natureza,cod_grupo) VALUES ('".$valor."','".$codEspecie."','".$codNatureza."','".$codGrupo."')";
            }

            if ($patrimonio->updateAtributosEspecie($sQuery)) {

            } else {
                echo '
                    <script type="text/javascript">
                        alertaAviso("Erro no cadastro de Atributos","unica","erro","'.Sessao::getId().'");
                        window.location = "incluiEspecie.php?'.Sessao::getId().'";
                    </script>';
            }

        // se ja possui atributos cadastrados
        } else {

            $cons = 0;

            $sQuery = "
                DELETE FROM
                    patrimonio.especie_atributo
                WHERE
                    cod_especie = '".$codEspecie."'
                    AND cod_atributo not in
                    (
                        select
                            cod_atributo from patrimonio.bem_atributo_especie
                        where
                            cod_especie = '".$codEspecie."'
                            AND cod_natureza = '".$codNatureza."'
                            AND cod_grupo = '".$codGrupo."'
                    )
                    AND cod_natureza = '".$codNatureza."'
                    AND cod_grupo = '".$codGrupo."'";

            $patrimonio->updateAtributosEspecie($sQuery);

            // se algum item foi selecionado na lista de atributos
            if (isset($atributo)) {

                while (list($chave,$valor) = each($atributo)) {

                    $patrimonio->setaVariaveisAtributosEspecie($valor, $codEspecie, $codGrupo, $codNatureza);

                    if ($patrimonio->insereAtributosEspecie())
                        $cons = $cons;
                    else
                        $cons = $cons++;
                }
            }

            if ($cons == 0) {

            } else {
                echo '
                    <script type="text/javascript">
                        alertaAviso("Erro no cadastro de Atributos","unica","erro","'.Sessao::getId().'");
                        window.location = "incluiEspecie.php?'.Sessao::getId().'";
                    </script>';
            }
        }

       // exit;

        //atualizao dos dados da Especie
        $patrimonio->setaVariaveisEspecie($codGrupo, $codNatureza, $codEspecie, $nomEspecie);

        if (comparaValor("nom_especie", $nomEspecie,"patrimonio.especie", "and cod_natureza = $codNatureza and cod_grupo = $codGrupo and cod_especie <> $codEspecie")) {

            if ($patrimonio->updateEspecie()) {
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $nomEspecie);
                $audicao->insereAuditoria();
                $msg .= "Espécie ".$nomEspecie;

                echo '
                    <script type="text/javascript">
                        alertaAviso("'.$msg.'","alterar","aviso","'.Sessao::getId().'");
                        window.location = "alteraEspecie.php?ctrl=1&pagina='.$pagina.'&'.Sessao::getId().'";
                    </script>';

            } else {

                echo '
                    <script type="text/javascript">
                        alertaAviso("Não foi possível alterar a espécie","unica","erro","'.Sessao::getId().'");
                        window.location = "alteraEspecie.php?'.Sessao::getId().'";
                    </script>';
            }

        } else {
            echo '
                <script type="text/javascript">
                    alertaAviso("A Espécie '.$nomEspecie.' já existe","unica","erro","'.Sessao::getId().'");
                    mudaTelaPrincipal("alteraEspecie.php?'.Sessao::getId().'&nomEspecie='.$nomEspecie.'&codNatureza='.$codNatureza.'&codGrupo='.$codGrupo.'&codEspecie='.$codEspecie.'&ctrl=1");
                </script>';
        }
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';

?>
