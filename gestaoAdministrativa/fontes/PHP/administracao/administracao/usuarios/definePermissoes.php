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
    * Manutneção de usuários
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.03.93

    $Id: definePermissoes.php 63829 2015-10-22 12:06:07Z franver $

    */
session_cache_limiter('private_no_expire');
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO.'usuarioLegado.class.php';
include_once CAM_FW_LEGADO.'paginacaoLegada.class.php';
include_once CAM_FW_LEGADO.'auditoriaLegada.class.php';
include_once CAM_FW_LEGADO.'mascarasLegado.lib.php';
include_once CAM_FW_LEGADO.'funcoesLegado.lib.php';
include_once CAM_FW_LEGADO.'dataBaseLegado.class.php';
include_once CAM_FW_LEGADO.'permissaoLegado.class.php';
include_once 'interfaceUsuario.class.php';

setAjuda("UC-01.03.93");

    $controle = (count($controle) == 0) ? 0 : '';

    $PHP_SELF = 'definePermissoes.php';
?>
<script type="text/javascript">
    function zebra(id, classe)
    {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) == 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>
<?php
    switch ($_REQUEST['controle']) {

        case 0:
            $html = new interfaceUsuario;
            $ctrl = "altera";
            $html->formBuscaUsuario($ctrl,$PHP_SELF);
        break;

        case 1:
            $pagina = $_REQUEST['pagina'];
            $inNumeroCgm = Sessao::read('numCgm');
?>
            <script type="text/javascript">
                function Importar()
                {
                    document.frm.action = "definePermissoes.php?<?=Sessao::getId();?>&controle=6&cgm=<?=$inNumeroCgm;?>";
                    document.frm.submit();
                }
            </script>
            <form name="frm" action="definePermissoes.php?<?=Sessao::getId()?>" method="post">
<?php

            $sSQL = "   SELECT
                            M.cod_modulo,
                            M.nom_modulo
                        FROM
                            administracao.modulo as M,
                            administracao.usuario as U
                        WHERE
                            M.cod_responsavel = U.numcgm AND
                            U.numcgm = ".$inNumeroCgm." AND
                            M.cod_modulo > 0
                        ORDER BY
                            M.nom_modulo ";

            $conectaBD = new databaseLegado;
            $conectaBD->abreBD();
            $conectaBD->abreSelecao($sSQL);
            $conectaBD->vaiPrimeiro();
            $bNaoTem = $conectaBD->eof();

            if ($bNaoTem) {
                alertaAviso('',"Usuário não é responsável por nenhum módulo","unica","erro");
                exit();
            }
            $condicao = "";
            if ($_REQUEST['cpf'] != "" || $_REQUEST['rg'] != "") {
                $condicao .= ", sw_cgm_pessoa_fisica AS PF ";
            }
            if ($_REQUEST['cnpj'] != "") {
                $condicao .= ", sw_cgm_pessoa_juridica AS PJ ";
            }

            if ($_REQUEST['numCgm'] == "") {
                $numeroCgm = $_REQUEST['cgm'];
            } else {
                $numeroCgm = $_REQUEST['numCgm'];
            }

            $sql  = "";
            $sql .= "    SELECT
                            C.numcgm as cgm,
                            C.nom_cgm,
                            U.numcgm,
                            U.username
                        FROM
                            administracao.usuario as U,
                            sw_cgm as C
                            ".$condicao."
                        WHERE
                            C.numcgm = U.numcgm AND
                            U.numcgm > 0 ";
            if ($numeroCgm != "") {
                $sql .= " AND U.numcgm = ".$numeroCgm;
            }
            if ($_REQUEST['nomCgm'] != "") {
                $sql .= " AND C.nom_cgm like '%".$_REQUEST['nomCgm']."%'";
            }
            if ($_REQUEST['username'] != "") {
                $sql .= " AND U.username like '%".$_REQUEST['username']."%'";
            }
            if ($_REQUEST['cpf'] != "") {
                $inCpf = str_replace(".", "", $_REQUEST['cpf']);
                $inCpf = str_replace("-", "", "'".$inCpf."'");
                $sql .= " AND PF.numcgm = C.numcgm";
                $sql .= " AND PF.cpf = ".$inCpf;
            }
            if ($_REQUEST['cnpj'] != "") {
                $inCnpj = str_replace(".", "", "'".$_REQUEST['cnpj']."'");
                $inCnpj = str_replace("/", "", $inCnpj);
                $inCnpj = str_replace("-", "", $inCnpj);
                $sql .= " AND PJ.numcgm = C.numcgm";
                $sql .= " AND PJ.cnpj = ".$inCnpj;
            }
            if ($_REQUEST['rg'] != "") {
                $sql .= " AND PF.numcgm = C.numcgm";
                $sql .= " AND PF.rg = '".$_REQUEST['rg']."'";
            }
            if (!Sessao::read('sSQLs')) {
                Sessao::write('sSQLs',$sql);
            }

            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados($sql,"10");
            $paginacao->pegaPagina($pagina);

            $paginacao->complemento = "&controle=1";

            $paginacao->geraLinks();
            $paginacao->pegaOrder(" U.username","ASC");
            $count = $paginacao->contador();
            $sSQL = $paginacao->geraSQL();

?>
        <table width='100%' id="tabelas">
            <tr>
                <td class="alt_dados" colspan="6">
                    Usuários disponíveis
                </td>
            </tr>
            <tr>
                <td class='labelcentercabecalho' width='5%'>&nbsp;</td>
                <td class='labelcentercabecalho' width='10%'>CGM</td>
                <td class='labelcentercabecalho' width='75%'>Nome</td>
                <td class='labelcentercabecalho' width='20%'>Usuário</td>
                <td class='labelcentercabecalho' width='5%'>&nbsp;</td>
                <td class='labelcentercabecalho' width='5%'>&nbsp;</td>
            </tr>
<?php
        $conectaBD = new databaseLegado;
        $conectaBD->abreBD();
        $conectaBD->abreSelecao($sSQL);
        $conectaBD->vaiPrimeiro();
        
        if($conectaBD->numeroDeLinhas  == 0 ){
            $boPaginacao = false;
?>
        <tr>
            <td class="show_dados_center" colspan="6"> Nenhum registro encontrado! </td>
        </tr>
<?php
        }else{
            $boPaginacao = true;
            while (!$conectaBD->eof()) {
                $inNumCgm = $conectaBD->pegaCampo("cgm");
                $stNomCgm = $conectaBD->pegaCampo("nom_cgm");
                $stUsuario = $conectaBD->pegaCampo("username");
?>
            <tr>
                <td class="show_dados_center_bold">
                    <?=$count++?>
                </td>
                <td class="show_dados">
                    <?=$inNumCgm;?>
                </td>
                <td class="show_dados">
                    <?=$stNomCgm;?>
                </td>
                <td class="show_dados">
                    <?=$stUsuario;?>
                </td>
                <td class="botao">
                    <a title="Importar permissões definidas de outro usuário" href="definePermissoes.php?<?=Sessao::getId();?>&controle=6&cgm=<?=$inNumCgm?>&pagina=<?=$pagina?>">
                        <img src="<?=CAM_FW_IMAGENS;?>botao_importar.png" border=0>
                    </a>
                </td>
                <td class="botao" width="75">
                    <a title="Definir manualmente as permissões" href='<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=2&cgm=<?=$inNumCgm;?>&usuario=<?=$stUsuario;?>&nomeUsuario=<?=$stNomCgm;?>&pagina=<?=$pagina?>'>
                    <img src="<?=CAM_FW_IMAGENS;?>botao_editar.png" border=0></a>
                </td>
            </tr>
<?php
            $conectaBD->vaiProximo();
            }
        }
        $conectaBD->limpaSelecao();
        $conectaBD->fechaBD();
?>
        </table>
        </form>
<?php
        if($boPaginacao){
            echo "<table width='450'>
                    <tr>
                        <td>
                            <font size='2'> ".$paginacao->mostraLinks()." </font>
                        </td>
                    </tr>
                </table>";           
            echo " <script> zebra('tabelas','zb'); </script> ";
        }
        
        break;

    case 2:
?>
        <script type="text/javascript">

                function Valida()
                {
                    var mensagem = "";
                    var erro = false;
                    var campo;

                    campo = document.frm.exercicio.value;
                    if (campo == "") {
                        mensagem += "@Campo Exercício é Obrigatório";
                        erro = true;
                    }
                    if (isNaN(campo)) {
                        mensagem += "@Campo Exercício só aceita Números";
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
                    document.frm.action = "definePermissoes.php?<?=Sessao::getId();?>&controle=1&pagina=<?=$pagina?>";
                    document.frm.submit();
                }
        </script>
<?php
        $inExercicio = Sessao::getExercicio();

?>
        <form name='frm' action='definePermissoes.php?<?=Sessao::getId();?>&controle=3' method='post'>
            <input type="hidden" name="cgm" value='<?=$_REQUEST['cgm'];?>'>
            <input type="hidden" name="usuario" value='<?=$_REQUEST['usuario'];?>'>
            <input type="hidden" name="nomeUsuario" value='<?=$_REQUEST['nomeUsuario'];?>'>
            <input type="hidden" name="pagina" value='<?=$pagina;?>'>
        <table width='100%'>
        <tr><td class=alt_dados colspan=2>Dados para permissões</td></tr>
            <tr>
                <td class="label" width="20%">
                    *Exercício
                </td>
                <td class="field" width="80%">
                    <input type="text" name="exercicio" value="<?=$inExercicio;?>" size="4" maxlength="4">
                </td>
            </tr>
            <tr>
                <td colspan="2" class='field'>
                    <?=geraBotaoOk(1,0,1,1);?>
                </td>
            </tr>
        </table>
        </form>
<?php
        break;
    case 3:

        $cgm = $_REQUEST['cgm'];
        $exercicio = $_REQUEST['exercicio'];
        $usuario = $_REQUEST['usuario'];
        $nomeUsuario = $_REQUEST['nomeUsuario'];

        if ($flag == 1) {
            echo '<script type="text/javascript">
                    alertaAviso("Marque Pelo Menos 1 Módulo!","unica","erro","'.Sessao::getId().'");
                    mudaTelaPrincipal("definePermissoes.php?'.Sessao::getId().'");
                  </script>';
        } else {

            $inNumCgm = Sessao::read('numCgm');

            /*  Exibe uma lista de módulos as quais o usuário poderá acessar
            Depois envia para uma página onde serão escolhidas as ações a que o usuário terá direito */
            $permissao = new permissaoLegado;
            if (Sessao::read('numCgm') == 0) { //Se o usuário for o siam pode alterar qualquer permissão
            $sSQL = "Select cod_modulo, nom_modulo, ativo
                        From administracao.modulo where cod_modulo > 0 Order by nom_modulo";
        } else { // O usuário só poderá alterar a permissão dos módulos pelo qual é responsável
            $sSQL = "Select M.cod_modulo, M.nom_modulo
                    From administracao.modulo as M, administracao.usuario as U
                    Where M.cod_responsavel = U.numcgm
                    And U.numcgm = ".$inNumCgm." and M.cod_modulo > 0
                    Order by M.ordem ";
        }

        $conectaBD = new databaseLegado;
        $conectaBD->abreBD();
        $conectaBD->abreSelecao($sSQL);
        $conectaBD->vaiPrimeiro(); ?>
        <script type="text/javascript">
           function marcarTodos()
           {
               var cont = 0;
               var campoT = document.frm.marcaTodos.checked;
               if (campoT == true) {
                   while (cont < document.frm.elements.length) {
                       if (document.frm.elements[cont].name == "modulosSelecionados[]") {
                           document.frm.elements[cont].checked = true;
                       }
                       cont++;
                   }
               } else {
                   while (cont < document.frm.elements.length) {
                       if (document.frm.elements[cont].name == "modulosSelecionados[]") {
                           document.frm.elements[cont].checked = false;
                       }
                       cont++;
                   }
               }
           }
        </script>
        <form name='frm' action='<?=$PHP_SELF;?>?<?=Sessao::getId();?>' method='post'>
            <input type="hidden" name="controle" value='4'>
            <input type="hidden" name="cgm" value='<?=$cgm;?>'>
            <input type="hidden" name="exercicio" value='<?=$exercicio;?>'>
            <input type="hidden" name="usuario" value='<?=$usuario;?>'>
            <input type="hidden" name="nomeUsuario" value='<?=$nomeUsuario;?>'>
            <table width='100%' cellspacing=2 border=0 cellpadding=1 align='center' id="tabelas">
                <tr>
                    <td class=alt_dados colspan=2>
                        <input type="checkbox" name='marcaTodos'
                        onclick = "javascript: marcarTodos();"
                        title="Marcar todos os módulos">
                        &nbsp;Módulos do sistema
                    </td>
                </tr>
                    <?php while (!$conectaBD->eof()) {
                        $codModulo = $conectaBD->pegaCampo("cod_modulo");
                        if ($conectaBD->pegaCampo("ativo") != 'f') {
                    ?>
                        <tr>
                            <td class='field' width='10'>
                                <input type="hidden" name="todosModulos[]" value='<?=$codModulo;?>'>
                                <input type="checkbox" name='modulosSelecionados[]' value='<?=$codModulo;?>'
                                    <?php if ($permissao->checaPermissaoModulo($cgm,$codModulo,$exercicio)) {echo "checked";} ?>
                                >
                            </td>
                            <td class='field'><?=$conectaBD->pegaCampo("nom_modulo");?></td>
                        </tr>
                        <?php }
                        $conectaBD->vaiProximo();
                    } ?>
                        <tr>
                            <td  colspan="2" class='show_dados'>
                                <input type="submit" name="submit" value="OK" style='width: 60px;'>
                                <?php echo "&nbsp; <input type='button' name='volta' style='width: 60px;' onclick=\"javascript:window.location.replace('".$PHP_SELF."');\" value='Cancelar'>"; ?>
                            </td>
                        </tr>
            </table>
        </form>
<?php
        $conectaBD->limpaSelecao();
        $conectaBD->fechaBD();
}
?>
<script>zebra('tabelas','zb');</script>
<?php
        break;

    case 4:
        //Exibe quais ações o usuário tem permissão
        //Permite que as permisões sejam alteradas

        $cgm = $_REQUEST['cgm'];
        $todosModulos = $_REQUEST['todosModulos'];
        $modulosSelecionados = $_REQUEST['modulosSelecionados'];
        $usuario = $_REQUEST['usuario'];
        $nomeUsuario = $_REQUEST['nomeUsuario'];
        $exercicio = $_REQUEST['exercicio'];

        //Modifica os vetores para enviar novamente via post
        if (count($todosModulos) > 0) {
            $todosModulos = implode(",",$todosModulos);
        } else {
            $todosModulos = "";
        }
        if (count($modulosSelecionados) > 0) {
            $modulosSelecionados = implode(",",$modulosSelecionados);
        } else {
            $modulosSelecionados = "";
            echo '<script type="text/javascript">
                    alertaAviso("Marque Pelo Menos 1 Módulo!","unica","erro","'.Sessao::getId().'");
                    mudaTelaPrincipal("definePermissoes.php?'.Sessao::getId().'&controle=3&flag=1&cgm'.$cgm.'");
                  </script>';
        }

        $permissao = new permissaoLegado;

        if ($exercicio >= 2013) {
            $sSQL = "   SELECT
                                A.cod_acao,
                                A.nom_acao,
                                A.cod_funcionalidade,
                                A.ativo,
                                F.nom_funcionalidade,
                                F.ativo AS func_ativo,
                                M.nom_modulo,
                                M.cod_modulo,
                                M.ordem,
                                F.ordem,
                                A.ordem
                        FROM
                                administracao.acao           as A,
                                administracao.funcionalidade as F,
                                administracao.modulo         as M,
                                administracao.gestao         as G
                        WHERE
                                A.cod_funcionalidade = F.cod_funcionalidade AND
                                M.cod_modulo = F.cod_modulo AND
                                G.cod_gestao = M.cod_gestao AND
                                F.ativo <> 'f' AND
                                A.ativo <> 'f' AND
                                A.cod_acao NOT IN (1501,1504,2195,2797,680,247,248,249,476,477,478,1501,1502,1503,2189,2190,2195,2214,2219,2220,2225,2230,2264,2422,1504,1505,1506,1507,2170,2259,2265,2797,2798,2799)
                        ";
        } else {
            $sSQL = "   SELECT
                        A.cod_acao,
                        A.nom_acao,
                        A.cod_funcionalidade,
                        A.ativo,
                        F.nom_funcionalidade,
                        F.ativo AS func_ativo,
                        M.nom_modulo,
                        M.cod_modulo,
                        M.ordem,
                        F.ordem,
                        A.ordem
                    FROM
                        administracao.acao           as A,
                        administracao.funcionalidade as F,
                        administracao.modulo         as M,
                        administracao.gestao         as G
                    WHERE
                        A.cod_funcionalidade = F.cod_funcionalidade AND
                        M.cod_modulo = F.cod_modulo AND
                        G.cod_gestao = M.cod_gestao AND
                        F.ativo <> 'f' AND
                        A.ativo <> 'f'
                        ";
        }

        if (count($modulosSelecionados) > 0) {
            $sSQL .= "And M.cod_modulo IN (".$modulosSelecionados.") ";
        } else {
            $sSQL .= "And M.cod_modulo < 0 ";
        }

        $sSQL .= " ORDER BY
                        G.ordem,
                        M.ordem,
                        F.ordem,
                        F.cod_funcionalidade,
                        A.ordem ";

        $conectaBD = new databaseLegado;
        $conectaBD->abreBD();
        $conectaBD->abreSelecao($sSQL);

        //Verifica se a query retorna alguma coisa
        if ($conectaBD->eof()) {
            echo "<br><h2>Nenhuma ação relacionada para os módulos selecionados!</h2>";

            return false;
        }

        $conectaBD->vaiPrimeiro();
        $moduloAtual = $conectaBD->pegaCampo("cod_modulo");
        $moduloProx = -1;
        $funcAtual = $conectaBD->pegaCampo("cod_funcionalidade");
        $funcProx = -1;  ?>
        <script type="text/javascript">
            function marcarTodosF(contador, nome)
            {
                var cont  = document.frm.elements.length;
                var contF = 0;
                var posicao = 0;
                while (contF < cont) {
                    if (document.frm.elements[contF].name == nome.name) {
                        posicao = contF;
                    }
                    contF++;
                }
                posicao = posicao + 1;
                while (document.frm.elements[posicao].name == "codAcao[]") {
                    if (nome.checked == true) {
                        document.frm.elements[posicao].checked = true;
                    } else {
                        document.frm.elements[posicao].checked = false;
                    }
                    posicao++;
                }
            }

            function marcarTodosM(contador, nome)
            {
                var cont    = document.frm.elements.length;
                var contF   = 0;
                var posicao = 0;
                var proximo = 0;
                contador    = contador + 1;
                while (contF < cont) {
                    if (document.frm.elements[contF].name == nome.name) {
                        posicao = contF;
                    }
                    if (document.frm.elements[contF].name == "marcaTodosM["+contador+"]" ||
                        document.frm.elements[contF].type != "checkbox") {
                        proximo = contF;
                    }
                    contF++;
                }
                posicao = posicao + 2;
                while (document.frm.elements[posicao].id != "marcaModulo") {
                    if (document.frm.elements[posicao].name == "codAcao[]") {
                        if (nome.checked == true) {
                            document.frm.elements[posicao].checked = true;
                        } else {
                            document.frm.elements[posicao].checked = false;
                        }
                    }
                    posicao++;
                }
            }
        </script>

        <form name='frm' action='definePermissoes.php?<?=Sessao::getId();?>' method='post'>
            <input type="hidden" name="controle" value='5'>
            <input type="hidden" name="cgm" value='<?=$cgm;?>'>
            <input type="hidden" name="exercicio" value='<?=$exercicio;?>'>
            <input type="hidden" name="usuario" value='<?=$usuario;?>'>
            <input type="hidden" name="nomeUsuario" value='<?=$nomeUsuario;?>'>
            <input type="hidden" name="modulosSelecionados" value='<?=$modulosSelecionados;?>'>
            <input type="hidden" name="todosModulos" value='<?=$todosModulos;?>'>
            <table width='100%' align='left'>
<?php
            $contador  = 0;
            $contadorM = 0;
?>
        <?php while (!$conectaBD->eof()) { ?>
            <?php if ($moduloAtual != $moduloProx) {
            ?>
                <tr>
                    <td class='alt_dados' style='text-align: left;' colspan='3' align='left'>
                    <input type="checkbox" name='marcaTodosM[<?=$contadorM?>]'
                    onclick = "javascript: marcarTodosM(<?=$contadorM?>, this);"
                    title="Marcar todas as funcionalidades do módulo <?=$conectaBD->pegaCampo("nom_modulo");?>"
                    id = "marcaModulo">
                    &nbsp;Módulo <?=$conectaBD->pegaCampo("nom_modulo");?></td>
                </tr>
                <?php
                    if ($moduloProx > -1) {
                        $moduloAtual = $moduloProx;

                    }
                    $contadorM = $contadorM + 1;
                }
                ?>
                <?php if ($funcAtual != $funcProx) {
                        if ($conectaBD->pegaCampo("ativo") != 'f') {?>
                            <tr>
                                <td class='label' style='text-align: left;' colspan='3'>
                                <input type="checkbox" name='marcaTodosF[<?=$contador?>]' onclick = "javascript: marcarTodosF(<?=$contador?>, this);"
                                title="Marcar todas as ações da funcionalidade <?=$conectaBD->pegaCampo("nom_funcionalidade");?>">
                                &nbsp;Funcionalidade <?=$conectaBD->pegaCampo("nom_funcionalidade");?></td>
                            </tr>
                            <?php
                        }

                        $contador = $contador + 1;

                        if ($funcProx > -1) {
                            $funcAtual = $funcProx;
                        }
                    }
                        ?>
                    <?php
                    if ($exercicio < '2013' && ($conectaBD->pegaCampo("cod_funcionalidade") == 314 || $conectaBD->pegaCampo("cod_funcionalidade") == 315)) {
                        if (!preg_match('/ANEXO[ ]{0,}[0-9]{1,2}/i',$conectaBD->pegaCampo("nom_acao"))) {
                            $nom_acao = $conectaBD->pegaCampo("nom_acao");
                            $cod_acao = $conectaBD->pegaCampo("cod_acao");
                    ?>
                    <tr>
                        <td class='field' width='1%'>
                            <input type="checkbox" name="codAcao[]" value='<?=$cod_acao;?>'
                            <?php if ($permissao->checaPermissaoAcao($cgm,$cod_acao,$exercicio)) {
                                    echo "checked";
                                }
                            ?>
                            >
                        </td>
                        <td class='field' width='99%'><?=$nom_acao;?></td>
                    </tr>
                    <?php
                        }
                    } else {
                        $nom_acao = $conectaBD->pegaCampo("nom_acao");
                        $cod_acao = $conectaBD->pegaCampo("cod_acao");
                    ?>
                    <tr>
                        <td class='field' width='1%'>
                            <input type="checkbox" name="codAcao[]" value='<?=$cod_acao;?>'
                                <?php if ($permissao->checaPermissaoAcao($cgm,$cod_acao,$exercicio)) {
                                        echo "checked";
                                    } ?>
                            >
                        </td>
                        <td class='field' width='99%'><?=$nom_acao;?></td>
                    </tr>
                <?php
                    }

                $conectaBD->vaiProximo();
                $moduloProx = $conectaBD->pegaCampo("cod_modulo");
                $funcProx = $conectaBD->pegaCampo("cod_funcionalidade");
            }
            
        ?>
            <tr>
                <td  colspan="2" class='field' style='width: 60px;'>
                    <input type="submit" name="ok" value="OK" style='width: 60px;'>
                    <?php echo "&nbsp; <input type='button' name='volta' style='width: 60px;' onclick=\"javascript:window.location.replace('".$PHP_SELF."');\" value='Cancelar'>"; ?>
                </td>
            </tr>
            <tr><td height=9></td></tr>
        </table>
<?php
        $conectaBD->limpaSelecao();
        $conectaBD->fechaBD();
        break;

    case 5:
        //Executa as alterações das permissões do usuário

        $cgm = $_REQUEST['cgm'];
        $codAcao = $_REQUEST['codAcao'];
        $exercicio = $_REQUEST['exercicio'];
        $todosModulos = $_REQUEST['todosModulos'];
        $usuario = $_REQUEST['usuario'];

        $permit = new permissaoLegado;
        if ($permit->alteraPermissao($cgm,$todosModulos,$codAcao,$exercicio)) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $usuario);
            $audicao->insereAuditoria();
            alertaAviso($PHP_SELF,"Permissões","alterar","aviso");
        } else {
            alertaAviso($PHP_SELF,"Permissões","n_alterar","erro");
        }
        break;

    case 6:
        $cgm = $_REQUEST['cgm'];
        $numCgmU = $_REQUEST['numCgmU'];
?>
        <script type="text/javascript">
            function Importar()
            {
                document.frm.action = "definePermissoes.php?<?=Sessao::getId()?>&controle=7&cgm=<?=$cgm;?>";
                document.frm.submit();
            }

            function Cancela()
            {
                document.frm.action = "definePermissoes.php?<?=Sessao::getId();?>&controle=1&pagina=<?=$pagina?>";
                document.frm.submit();
            }
        </script>

        <script type="text/javascript">
            function buscaUsuario()
            {
                document.frm.action = "definePermissoes.php?<?=Sessao::getId();?>&controle=6";
                document.frm.submit();
            }
        </script>
        <form action="definePermissoes.php?<?=Sessao::getId();?>&controle=7" method="POST" name="frm">
            <table width="100%">
            <input type="hidden" name="cgm" value="<?=$cgm;?>">
            <input type="hidden" name="HdnnumCgmU" value="">
            <input type="hidden" name="pagina" value="<?=$pagina;?>">
            <tr><td class=alt_dados colspan=2>Selecione o Usuário para importação</td></tr>
            <tr>
                <td class=label title="Usuário que possua permissões a serem importadas">
                Usuário
                </td>
                <td class="field" width="60%">
                    <input type="text" size='8' maxlength='8' onKeyPress="return(isValido(this, event, '0123456789'))" name="numCgmU" value='<?=$numCgmU;?>' onblur="javascript: buscaUsuario();">
<?php
                if ($numCgmU != "") {
                    $select = 	"SELECT
                                    username
                                FROM
                                    administracao.usuario AS U
                                WHERE
                                    numcgm   = ".$numCgmU;
                    $dbConfig = new databaseLegado;
                    $dbConfig->abreBd();
                    $dbConfig->abreSelecao($select);
                    $nomCgmU = $dbConfig->pegaCampo("username");
                    $dbConfig->limpaSelecao();
                    $dbConfig->fechaBd();
                }
?>
                    <input type="text" name="nomCgmU" size="30" value="<?=$nomCgmU;?>"  readonly="">&nbsp;&nbsp;
                    <a href='javascript:procurarCgm("frm","numCgmU","nomCgmU","usuario","<?=Sessao::getId()?>");'>
                        <img src="<?=CAM_FW_IMAGENS;?>procuracgm.gif" alt="Procurar Usuário" width=22 height=22 border=0>
                    </a>
                </td>
            </tr>

            <tr>
                <td class=field colspan=2>
                    <input type="button" name="importar" value="OK"
                    style="width: 60px" onclick="javascript: Importar();">&nbsp;
                    <input type="button" name="cancelar" value="Cancelar"
                    onclick = "javascript:Cancela();">
                </td>
            </tr>
            </table>
        </form>
<?php
    break;

    case 7:

        $cgm = $_REQUEST['cgm'];
        $nomCgmU = $_REQUEST['nomCgmU'];
        $numCgmU = $_REQUEST['numCgmU'];

        $dbConfig = new databaseLegado;
        $dbConfig->abreBd();
        $select = "select numcgm from administracao.permissao where numcgm = ".$cgm;
        if ($dbConfig->abreSelecao($select) != "") {
            $delete = "delete from administracao.permissao where numcgm = ".$cgm;
            $dbConfig->executaSql($delete);
        }
        $insert = "insert into administracao.permissao (numcgm, cod_acao, ano_exercicio)
                select ".$cgm.", cod_acao, ano_exercicio from administracao.permissao where numcgm = ".$numCgmU;
        $result = $dbConfig->executaSql($insert);
        $dbConfig->fechaBd();
        if ($result) {
            echo '<script type="text/javascript">
                    alertaAviso("Permissões Importadas com Sucesso","unica","aviso","'.Sessao::getId().'");
                    window.location = "definePermissoes.php?'.Sessao::getId().'&controle=1&pagina='.$pagina.'";
                </script>';
        } else {
           echo '<script type="text/javascript">
                    alertaAviso("Permissões não puderam ser importadas, entre em contato com o administrador","unica","erro","'.Sessao::getId().'");
                    window.location = "definePermissoes.php?'.Sessao::getId().'&controle=1&pagina='.$pagina.'";
                </script>';
        }
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
