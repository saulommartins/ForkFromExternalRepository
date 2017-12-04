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

$Revision: 27772 $
$Name$
$Author: luiz $
$Date: 2008-01-28 08:38:22 -0200 (Seg, 28 Jan 2008) $

Casos de uso: uc-01.03.93
*/

class interfaceUsuario
{
/**************************************************************************
 Gera o Combo com os códigos da Função
/**************************************************************************/
    public function comboFuncao($nome="funcao",$default="")
    {
        $sql = "Select cod_funcao, nom_funcao
                From sw_funcao
                Order by nom_funcao ";
        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            $combo = "";
            $selected = "";
            $combo .= "<select name='".$nome."' style='width: 180px;'>\n";
                if($default=="")
                    $selected = "selected";
            $combo .= "<option value='xxx' ".$selected.">Selecione uma função</option>\n";
            while (!$dataBase->eof()) {
                $codFuncao = $dataBase->pegaCampo("cod_funcao");
                $nomFuncao = trim($dataBase->pegaCampo("nom_funcao"));
                $selected = "";
                    if($codFuncao==$default)
                        $selected = "selected";
                $dataBase->vaiProximo();
                $combo .= "<option value='".$codFuncao."' ".$selected.">".$nomFuncao."</option>\n";
            }
            $combo .= "</select>";
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $combo;
    }//Fim function comboFuncao

/**************************************************************************
 Formulário para procurar por um usuário ou cgm pessoa física
/**************************************************************************/
    public function formBuscaUsuario($ctrl,$action="")
    {
?>
    <script type="text/javascript">

    //A função Valida() faz a verfificação dos campos, monte-a conforme a sua necessidade.

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campo2;
        var campo3;
        var campoaux;

        campo = document.frm.numCgm.value.length;
        <?php if ($ctrl == 'insere') { ?>
            campo2 = document.frm.nomCgm.value.length;
            //campo3 = document.frm.cpf.value.length;
            campo3 = document.frm.cpf.value.length;
            campo4 = document.frm.cnpj.value.length;
            campo5 = document.frm.rg.value.length;
        <?php } else { ?>
            campo3 = document.frm.username.value.length;
            campo4 = 1;
            campo5 = 1;
        <?php } ?>

            campo = document.frm.numCgm.value;
            if (isNaN(campo)) {
                mensagem += "@Campo CGM inválido";
                erro = true;
            }

        //if (erro) alertaMensagem(mensagem,'erro');
        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form

      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }
</script>

<form name="frm" action="<?=$action;?>?<?=Sessao::getId();?>" method="POST">
    <input type="hidden" name="controle" value="1">
    <input type="hidden" name="ctrl" value="<?=$ctrl;?>">
    <table width="100%">
    <tr><td class=alt_dados colspan=2>Dados para Filtro</td></tr>
        <tr>
            <td class="label" width="20%">CGM</td>
            <td class="field" width="80%">
                <input type="text" class="field" name="numCgm" size="10" maxlength="10" value="" onKeyPress="return(isValido(this, event, '0123456789'));">
            </td>
        </tr>
        <?php if ($ctrl=='altera') { ?>
        <tr>
            <td class="label" >Username</td>
        <td class="field"><input type="text" name="username" size="10" maxlength="15" value="" ></td>
        </tr>
                  <?php } else { ?>
        <tr>
            <td class="label">Nome</td>
            <td class="field"><input type="text" name="nomCgm" size="30" maxlength="60" value=""></td>
        </tr>
        <?php } if ($ctrl=='consulta') { ?>
        <tr>
            <td class="label" >Username</td>
            <td class="field"><input type="text" name="username" size="10" maxlength="15" value="" ></td>
        </tr>
        <?php } else { ?>
        <tr>
            <td class="label">CNPJ</td>
            <td class="field">
                <input type="text" name="cnpj" maxlength="18" size="19" value=""
                onKeyUp="JavaScript: mascaraCNPJ( this, event );"
                onKeyPress="return(isValido(this, event, '0123456789'));">&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">CPF</td>
            <td class="field">
                <input type="text" maxlength="14" name="cpf" size="15" value=""
                onKeyUp="JavaScript: mascaraCPF( this, event );"
                onKeyPress="return(isValido(this, event, '0123456789'));">&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">RG</td>
            <td class="field">
                <input type="text" maxlength="10" name="rg" size="10" value=""
                onKeyUp="JavaScript: return autoTab(this, 10, event);"
                onKeyPress="return(isValido(this, event, '0123456789'));">&nbsp;
            </td>
        </tr>
        <?php } ?>

        <tr>
            <td colspan="2" class='field'>
                &nbsp;<input type="button" name='OK' value='OK' style='width: 60px;' class="botao" onClick="Salvar();">
                &nbsp;<input type="reset" name="reset" value="Limpar" style='width: 60px;'>
            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">
    placeFocus();
</script>
<?php
    }//Fim da function formBuscaUsuario

/*******************************************************************************
 Busca o usuário ou cgm conforme os parâmetros enviados pelo formulário de busca
 e exibe uma lista dos cgm's encontrados
/******************************************************************************/
    public function resultadoBuscaUsuario($ctrl,$numCgm,$nomCgm,$username,$cpf,$cnpj,$action="",$rg="", $pagina="")
    {
    $pagina = $_REQUEST['pagina'];

    //echo $action."<br>";
        $condicao = "";
        if ($cpf != "" || $rg != "") {
            $condicao = ", sw_cgm_pessoa_fisica AS PF";
        }
        if ($cnpj != "") {
            $condicao = ", sw_cgm_pessoa_juridica AS PJ";
        }
        $sql  = "";
        $sql .= "   SELECT
                        U.numcgm,
                        G.nom_cgm,
                        U.username
                    FROM
                        administracao.usuario AS U,
                        sw_cgm     AS G
                        ".$condicao."
                    WHERE
                        U.numcgm > 0 AND
                        U.numcgm = G.numcgm
                        ";
        if ($numCgm != "") {
            $sql .= " AND U.numcgm = ".$numCgm;
        }
        if ($nomCgm != "") {
            $sql .= " AND G.nom_cgm like '%".$nomCgm."%'";
        }
        if ($username != "") {
            $sql .= " AND U.username like '%".$username."%'";
        }
        if ($cpf != "") {
            $cpf = str_replace(".", "", $cpf);
            $cpf = str_replace("-", "", $cpf);
            $sql .= " AND PF.numcgm = G.numcgm";
            $sql .= " AND PF.cpf = '".$cpf."'";
        }
        if ($cnpj != "") {
            $cnpj = str_replace(".", "", $cnpj);
            $cnpj = str_replace("/", "", $cnpj);
            $cnpj = str_replace("-", "", $cnpj);
            $sql .= " AND PJ.numcgm = G.numcgm";
            $sql .= " AND PJ.cnpj = '".$cnpj."'";
        }
        if ($rg != "") {
            $sql .= " AND PF.numcgm = G.numcgm";
            $sql .= " AND PF.rg = '".$rg."'";
        }

        if (!Sessao::read('sql')) {
            Sessao::write('sql',$sql);
        }
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento = "&controle=1";
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(G.nom_cgm)","ASC");
        $stSql = $paginacao->geraSQL();
        $obDb = new dataBaseLegado;
        $obDb->abreBD();

        $obDb->abreSelecao($stSql);
        //$obDb->vaiPrimeiro();
        $stHtml = "";
        $stHtml .= "<table width='100%'>
            <tr>
                <td class='alt_dados' colspan='5'>Registros de CGM</td>
            </tr>
            <tr>
                <td class='labelleft' width='5%'>&nbsp;</td>
                <td class='labelleft' width='10%'>CGM</td>
                <td class='labelleft' width='75%'>Nome</td>
                <td class='labelleft' width='15%'>Username</td>
                <td class='labelleft''>&nbsp;</td>
            </tr>";
        $count = $paginacao->contador();
        while (!$obDb->eof()) {
            $inCodCgm   =      $obDb->pegaCampo("numcgm");
            $stNomeCgm  = trim($obDb->pegaCampo("nom_cgm"));
            $username   = trim($obDb->pegaCampo("username"));
            $obDb->vaiProximo();
            $stHtml .= "
            <tr>
                <td class='labelcenter'>".$count++."</td>
                <td class=show_dados>".$inCodCgm."</td>
                <td class=show_dados>".$stNomeCgm."</td>
                <td class=show_dados>".$username."</td>
                <td class=botao width=20>
                <a href='alteraUsuario.php?".Sessao::getId()."&cgm=".$inCodCgm."&controle=2&pagina=".$pagina."'>
                <img src='../../images/btnincluir.gif' border=0></a></td>\n
            </tr> \n";
        }
        $stHtml .= "</table>";
        $obDb->limpaSelecao();
        $obDb->fechaBD();
        if ($obDb->numeroDeLinhas <= 0) {
            $stHtml .=  "<b>Não existem usuários para este filtro!</b>";
        }
        echo $stHtml;
        echo "<table width=450 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
    }// Fim da function resultadoBuscaUsuario


/**************************************************************************
 Formulário que mostra os dados do cgm e um formulário para incluir ou
 atualizar um usuário
/**************************************************************************/
    public function formDadosUsuario($action,$ctrl,$cgm,$dadosForm="",$operacao="",$pagina="")
    {
        //Torna o objeto sessao visível dentro desta classe

        $stMascaraSetor = SistemaLegado::pegaConfiguracao("mascara_setor");
        $inTamanhoMascara = strlen($stMascaraSetor);

        $anoExercicioSetor = Sessao::getExercicio();
        $usuario = new usuarioLegado;
        $dadosCgm = $usuario->pegaDadosUsuario($cgm);
        if (is_array($dadosCgm)) {
            if ($ctrl == "altera") {
                $codMasSetor = $dadosCgm["setor"]."/".$dadosCgm["anoExercicioSetor"];
            }
            //Grava como variável o nome da chave do vetor com o seu respectivo valor
            foreach ($dadosCgm as $campo=>$valor) {
                $$campo = trim($valor);
            }
        }

        //Grava os campos do vetor como variáveis de um form previamente preenchido
        if (is_array($dadosForm)) {
            foreach ($dadosForm as $chave=>$valor) {
                $$chave = $valor;
            }
        }
?>
<script type="text/javascript">
    //A função Valida() faz a verfificação dos campos, monte-a conforme a sua necessidade.
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
    var senha;
    var senha1;
        var campoaux;

        campo = document.frm.username.value.length;
            if (campo == 0) {
                mensagem += "@O campo username é obrigatório";
                erro = true;
            }

    //Verificação de senha
    <?php if ($ctrl != "altera") { ?>

    senha = document.frm.senha.value;
            if (senha.length == 0) {
                mensagem += "@O campo senha é obrigatório";
                erro = true;
            }

        senha1 = document.frm.senha2.value;
            if (senha1.length == 0) {
                mensagem += "@O campo confirmação de senha é Obrigatório";
                erro = true;
            }

        if (senha != senha1) {
          mensagem += "@A confirmação de senha está errada!";
          erro = true;
        }

    //Fim - Verificação de Senha
    <?php } ?>
        campo = document.frm.codSetor.value;
            if (campo == "xxx") {
                mensagem += "@O campo setor é obrigatório";
                erro = true;
            }

        <?php if ($ctrl == "insere") { ?>
                campo = document.frm.senha.value;
                campoaux = document.frm.senha2.value;
                if (campo != campoaux) {
                    mensagem += "@A confirmação de senha está errada!";
                    erro = true;
                }

        <?php  } ?>

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
        return !(erro);
    }// Fim da function Valida


    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form

    function Salvar()
    {
      document.frm.ok.disabled = true;
      if ( Valida() ) {
            document.frm.target = "telaPrincipal";
            document.frm.action = "<?=$action;?>?<?=Sessao::getId();?>&controle=3";
            document.frm.submit();
      }
      document.frm.ok.disabled = false;
    }

    function Cancela()
    {
        mudaTelaPrincipal('<?=$PHP_SELF;?>?<?=Sessao::getId();?>&pagina=<?=$pagina;?>');
    }

    function validaSetor(origem,item)
    {
        var f = document.frm;
        f.controle.value = 9;
        f.stOrigem.value = origem;
        f.stItem.value = item;
        f.submit();
    }

</script>
    <form name="frm" action="<?=$action;?>?<?=Sessao::getId();?>&controle=3" method="post" target="oculto">
        <!--<input type="hidden" name="controle" value="3">-->
        <input type="hidden" name="numCgm" value="<?=$numCgm;?>">
        <input type="hidden" name="nomCgm" value="<?=$nomCgm;?>">
        <input type="hidden" name="ctrl"   value="<?=$ctrl;?>">
        <input type="hidden" name="stOrigem" value="text">
        <input type="hidden" name="stItem" value="">
        <table width='100%'>
        <tr><td class=alt_dados colspan=2>Dados para usuário</td></tr>
            <tr>
                <td class="label" width='30%'>CGM:</td>
                <td class="field" width='70%'><?=$numCgm;?></td>
            </tr>
            <tr>
                <td class="label">Nome CGM:</td>
                <td class="field"><?=$nomCgm;?></td>
            </tr>
            <tr>
                <td class="label">*Username:</td>
                <td class="field">
                    <input type="text" name="username" size="15" maxlength="15"
                    <?php
                        if ($usuario->verificaUsuario("numcgm",$numCgm)) {
                            echo "value='$username' ";
                        } else {
                            echo "value=''";
                        }
                    ?>
                    >
                </td>
            </tr>
            <?php if ($operacao == "incluir") { ?>
            <tr>
                <td class="label">*Senha:</td>
                <td class="field"><input type="password" name="senha" value="" size="20" maxlength="34"></td>
            </tr>
            <tr>
                <td class="label">*Confirmação de senha:</td>
                <td class="field"><input type="password" name="senha2" value="" size="20" maxlength="34"></td>
            </tr>
            <?php } ?>
            <tr>
                <td class="label" title="Define se o usuário poderá acessar o sistema">*Status:</td>
                <td class="field">
           <?php

           //radio button da tela de usuários!
            if ($ctrl == "altera") {
              if ($status == "Ativo" ||  $status == "A") {
                $stAtivo = " checked";
              }
              if ($status == "Inativo" ||  $status == "I") {
                $stInativo = " checked";
              }
            }
            if ($stAtivo == "" && $stInativo == "") {
                $stInativo = " checked";
            }

                ?>
                    <input type="radio" name="status" value="I" <?php echo $stInativo; ?> >Inativo
            <input type="radio" name="status" value="A" <?php echo $stAtivo; ?> >Ativo
                </td>
            </tr>
            <?php include(CAM_FW_LEGADO."filtrosSELegado.inc.php"); ?>
            <tr>
                <td colspan="2" class='field'>
                <?php geraBotaoAltera(); ?>
                </td>
            </tr>
        </table>
    </form>

<?php
    }// Fim da function formDadosUsuario

/**************************************************************************
 Formulário para alterar a senha de um usuário cadastrado
/**************************************************************************/
    public function formAlteraSenha($action)
    {
        $exercicio = SistemaLegado::pegaConfiguracao("ano_exercicio");

?>
    <script type="text/javascript">

        function Valida()
        {
            var mensagem = "";
            var erro = false;
            var campo;
            var campoaux;

            campo = document.frm.usuario.value.length;
                if (campo == 0) {
                    mensagem += "@O campo usuário é obrigatório";
                    erro = true;
                }

            campo = document.frm.senha.value;
            campoaux = document.frm.senha2.value;
                if (campo != campoaux) {
                    mensagem += "@A confirmação de senha está errada";
                    erro = true;
                }

            campo = document.frm.senha.value.length;
                if (campo == 0) {
                    mensagem += "@O campo nova senha é obrigatório";
                    erro = true;
                }
<?if ( Sessao::read('numCgm') != 0 ) {?>
            campo = document.frm.senhaAtual.value.length;
                if (campo == 0) {
                    mensagem += "@O campo senha atual é obrigatório";
                    erro = true;
                }
<?}?>

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
            return !(erro);
        }// Fim da function Valida


        //A função salvar testa a validação, e se tudo ocorrer certo, envia o form

        function Salvar()
        {
            if (Valida()) {
                document.frm.submit();
            }
        }
    </script>

    <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId();?>'>
        <input type="hidden" name="controle" value='1'>
        <input type="hidden" name="exercicio" value='<?=$exercicio;?>'>
        <table width='100%'>
            <tr>
                <td class=alt_dados colspan=2>
                    Dados para senha
                </td>
            </tr>
            <tr>
                <td class="label" width='20%'>
                    *Username
                </td>
                <?php
                    if (Sessao::read('numCgm') == 0 ) {
                ?>
                <td class="field" width="80%">
                    <input type="text" name="usuario" size='10'
                    maxlength='15' value='<?=Sessao::getUsername();?>'>
                </td>
                <?php
                    } else {
                        echo "<input type='hidden' name='usuario' value=".Sessao::getUsername().">";
                        echo "
                        <td class='field'>
                            ".Sessao::getUsername()."
                        </td>";
                    }
                ?>
            </tr>
                <?php
                    if (Sessao::read('numCgm') != 0 ) {
                ?>
            <tr>
                <td class="label" >
                    *Senha Atual
                </td>
                <td class="field">
                    <input type="password" name="senhaAtual" size='30' maxlength='34'>
                </td>
            </tr>
                <?php
                    }
                ?>
            <tr>
                <td class="label" >
                    *Nova Senha
                </td>
                <td class="field">
                    <input type="password" name="senha" size='30' maxlength='34'>
                </td>
            </tr>
            <tr>
                <td class="label" >
                    *Confirma Senha
                </td>
                <td class="field">
                    <input type="password" name="senha2" size='30' maxlength='34'>
                </td>
            </tr>
            <tr>
                <td colspan="2" class='field'>
                        <?=geraBotaoOk();?>
                </td>
            </tr>
        </table>
    </form>
<?php
    }// Fim da function formAlteraSenha

}//Fim da classe
?>
