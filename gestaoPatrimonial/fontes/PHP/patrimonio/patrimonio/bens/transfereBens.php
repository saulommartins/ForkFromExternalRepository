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
    * Transfere bens individualmente ou em lote
    * Data de Criação   : 26/03/2003

    * @author Desenvolvedor Ricardo Lopes de Alencar
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 17884 $
    $Name$
    $Autor: $
    $Date: 2006-11-20 10:24:53 -0200 (Seg, 20 Nov 2006) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.25  2006/11/20 12:24:53  bruce
Bug #6921#

Revision 1.24  2006/10/16 14:27:53  larocca
Bug #6921#

Revision 1.23  2006/07/21 11:35:07  fernando
Inclusão do  Ajuda.

Revision 1.22  2006/07/13 18:34:58  fernando
Alteração de hints

Revision 1.21  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.20  2006/07/06 12:11:27  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php'; //Inclui classe para inserir auditoria
include_once '../bens.class.php'; //Inclui classe que controla os bens
include_once 'interfaceBens.class.php'; //Inclui classe que contém a interface html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.06");
if (!isset($controle)) {
    $controle = 100;
}

if ($controle == 1) {
    $controle = 0;
}

switch ($controle) {

    // escolhe o Tipo de Transferencia
    case 100:
        $html = new interfaceBens;
        $html->listaTipoTransferencia();
    break;

/************************************************************/
/* Transfere bens INDIVIDUALMENTE
/************************************************************/

    case 0:
        include_once 'listarBens.php';
        exit();
    break;

    case 2:
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';

        //Verifica se o bem digitado está ativo
        if (pegaDado("cod_bem","patrimonio.bem_baixado","Where cod_bem = '".$codBem."' ")) {
            alertaAviso($PHP_SELF."?".Sessao::getId()."","Este bem está baixado","unica","aviso");
        }
        //Verifica se o bem digitado existe
        if (pegaDado("cod_bem","patrimonio.bem","Where cod_bem = '".$codBem."' ")) {
            $bens = new bens;
            $vetBens = $bens->pegaDados($codBem);
        } else {
            alertaAviso($PHP_SELF."?".Sessao::getId()."&controle=0","Nenhum registro encontrado","unica","aviso");
        }

        // operacoes no frame oculto
        switch ($ctrl_frm) {

            // preenche os combos do Local
            case 1:
 include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosCALOLegado.inc.php';
               exit();
            break;
        }

        //Carrega os campos do vetor como variáveis, cada qual com seu respectivo valor
        if (is_array($vetBens)) {
            foreach ($vetBens as $chave=>$valor) {
                $$chave = $valor;
            }
            $descSituacao = preg_replace( "/\n/","<br>",$descSituacao);
        }

        // preenche os combos do Local
        $valor = $codMasSetor;
        $variavel = "codMasSetor";
 include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosCALOLegado.inc.php';

        $exercicio = pegaConfiguracao("ano_exercicio");

    ?>
        <script type="text/javascript">
            function valorMaximo(campo, limite)
            {
                if (campo.value.length > limite) // se estiver maior que o tamanho estabelecido, reduza-o
                    campo.value = campo.value.substring(0, limite);
            }

            // preenche os combos do local (Orgao, Unidade, Departamento, Setor e Local)
            function preencheLocal(variavel, valor)
            {
                document.frm.target = "oculto";
                document.frm.controle.value = "2";
                document.frm.ctrl_frm.value = "1";
                document.frm.variavel.value = variavel;
                document.frm.valor2.value = escape(valor);
                document.frm.submit();
            }

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;

                campo = document.frm.codMasSetor.length;
                        if (campo==0) {
                            mensagem += "@O campo local é obrigatório.";
                            erro = true;
                        }

                campo = document.frm.situacao.value;
                    if (campo=='xxx') {
                        mensagem += "@O campo situação é obrigatório.";
                        erro = true;
                    }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);

            }// Fim da function Valida

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                if (Valida()) {
                    document.frm.controle.value = 3;
                    document.frm.submit();
                }
            }
        </script>

        <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId()?>'>

            <input type="hidden" name="controle" value=''>
            <input type="hidden" name="ctrl_frm" value=''>
            <input type="hidden" name="codBem" value='<?=$codBem;?>'>
            <input type="hidden" name="variavel" value=''>
            <input type="hidden" name="valor2" value=''>

            <table width="100%">

            <tr><td class=alt_dados colspan=2 title="Informe os dados do bem.">Informe os dados do Bem</td></tr>

            <tr>
                <td class="label" width="20%" title="Informe o código do bem.">Código do Bem</td>
                <td class="field"><?php echo $codBem; ?></td>
            </tr>

            <tr>
                <td class="label" title="Informe a descrição do bem." >Descrição</td>
                <td class="field"><?php echo $descricao; ?></td>
            </tr>

            <tr>
                <td class="label" title="Informe o local onde o bem está localizado." width="30%">Localização Atual</td>
                <td class="field">
                    <?php echo $nomLocal; ?>
                </td>
            </tr>

            <tr>
                <td class="label" title="Informe a situação do bem.">Situação Atual</td>
                <td class="field">
                    <?php echo $nomSituacao; ?>
                </td>
            </tr>
    <!--
            <tr>
                <td class="label" >Descrição Atual</td>
                <td class="field">
                    <?php echo $descSituacao; ?>
                </td>
            </tr>
    -->
            <tr>
                <td class="label" title="Informe a localização do bem." width="30%">*Localização</td>
                <td class="field">
                    <input type="text" name="codMasSetor" value="<?=$codMasSetor?>" size="<?=strlen($mascaraSetor);?>" maxlength="<?=strlen($mascaraSetor);?>"
                        onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraSetor?>', this, event);"
                        onChange="JavaScript: preencheLocal( 'codMasSetor', this.value )">
                </td>
            </tr>
            <tr>
                <td class="label"  title="Selecione o orgão do bem." width="30%">*Orgão</td>
                <td class='field'>
                    <select name='codOrgao' onChange="javascript: preencheLocal('codOrgao', this.value);" style="width:400px">
                        <option value='xxx' SELECTED>Selecione</option>
    <?php
                        //Faz o combo de Órgãos
                        $sSQL = "SELECT
                                    cod_orgao, nom_orgao, ano_exercicio
                                FROM
                                    administracao.orgao
                                ORDER
                                    by nom_orgao";
                        $dbEmp = new dataBaseLegado;
                        $dbEmp->abreBD();
                        $dbEmp->abreSelecao($sSQL);
                        $dbEmp->vaiPrimeiro();

                        $comboCodOrgao = "";
                        while (!$dbEmp->eof()) {
                            $anoExercicio = trim($dbEmp->pegaCampo("ano_exercicio"));
                            $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                            $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                            $chave = $codOrgaof."-".$anoExercicio;
                            $dbEmp->vaiProximo();
                            $comboCodOrgao .= "<option value='".$chave."'";
                            if (isset($codOrgao)) {
                                if ($chave == $codOrgao) {
                                    $comboCodOrgao .= " SELECTED";
                                    $nomOrgao = $nomOrgaof;
                                }
                            }
                            $comboCodOrgao .= ">".$nomOrgaof." - ".$anoExercicio."</option>\n";
                        }

                        $dbEmp->limpaSelecao();
                        $dbEmp->fechaBD();
                        echo $comboCodOrgao;
    ?>
                    </select>
                    <input type="hidden" name="nomOrgao" value="">
                </td>
            </tr>
            <tr>
                <td class="label"  title="Selecione a Unidade do bem." width="30%">*Unidade</td>
                <td class=field>
                    <select name="codUnidade" onChange="javascript: preencheLocal('codUnidade', this.value);" style="width:400px">
                        <option value=xxx SELECTED>Selecione</option>
                    </select>
                    <input type="hidden" name="nomUnidade" value="">
                </td>
            </tr>
            <tr>
               <td class="label"  title="Selecione o departamento do bem." width="30%">*Departamento</td>
                <td class="field">
                    <select name="codDepartamento" onChange="javascript: preencheLocal('codDepartamento', this.value);" style="width:400px">
                        <option value="xxx" SELECTED>Selecione</option>
                    </select>
                    <input type="hidden" name="nomDepartamento" value="">
                </td>
            </tr>
            <tr>
                <td class="label"  title="Selecione o setor do bem." width="30%">*Setor</td>
                <td class="field">
                    <select name="codSetor" onChange="javascript: preencheLocal('codSetor', this.value);" style="width:400px">
                        <option value="xxx" SELECTED>Selecione</option>
                    </select>
                    <input type="hidden" name="nomSetor" value="">
                    <input type="hidden" name="anoExercicioSetor" value="">
                </td>
            </tr>
            <tr>
                <td class="label"  title="Selecione o local do bem." width="30%">*Local</td>
                <td class="field">
                    <select name="codLocal" onChange="javascript: preencheLocal('codLocal', this.value);" style="width:400px">
                        <option value="xxx" SELECTED>Selecione</option>
                    </select>
                    <input type="hidden" name="nomLocal" value="">
                    <input type="hidden" name="anoExercicioLocal" value="">
                </td>
            </tr>

            <tr>
                <td class="label"  title="Selecione a situação.">*Nova Situação</td>
                <td class="field">
                    <input type="text" name="codTxtSituacao" size="10" maxlength="10"
                    onChange="javascript: preencheCampo(this, document.frm.situacao);"
                    onKeyPress="return(isValido(this, event, '0123456789'));">

<?php
                    $combo = "";
                    $combo .= "<select name='situacao' id='situacao' style='width: 200px;
                        'onchange='javascript:preencheCampo(this, document.frm.codTxtSituacao
                    );'>\n";

                    $combo .= "<option value='xxx' ".$selected.">Selecione a situação do bem</option>\n";

                    $sql = "Select cod_situacao, nom_situacao
                            From patrimonio.situacao_bem Order by nom_situacao";
                    //echo "<!--".$sql."-->";
                    $dataBase = new dataBaseLegado;
                    $dataBase->abreBD();
                    $dataBase->abreSelecao($sql);
                    $dataBase->fechaBD();
                    $dataBase->vaiPrimeiro();

                    while (!$dataBase->eof()) {
                        $codSituacao = trim($dataBase->pegaCampo("cod_situacao"));
                        $nomSituacao = trim($dataBase->pegaCampo("nom_situacao"));
                        $selected = "";
                            if($codSituacao==$default)
                                $selected = "selected";
                        $dataBase->vaiProximo();
                        $combo .=
                            "<option value='".$codSituacao."' ".$selected.">".$nomSituacao."</option>\n";
                    }

                    $dataBase->limpaSelecao();
                    $combo .= "</select>";
                    echo $combo;
?>
                </td>
            </tr>

            <tr>
                <td class="label" title="Informe a descrição da situação do bem.">Descrição da Situação</td>
                <td class="field">
                      <input type="text"  name="descSituacao" value="<?=$descSituacao;?>" size="80" maxlength="60" >
                </td>
            </tr>

            <tr>
                <td colspan='2' class='field'>
                    <?php geraBotaoOk(); ?>
                </td>
            </tr>

            </table>

        </form>
<?php

    break;

    // executa operacao de transferencia no BD
    case 3:
        $ok = true;

        // verifica se o local informado eh valido
        $local = explode ("/", $codMasSetor);
        if (!($vetLocal = validaLocal($local[0],$local[1]))) {
            $ok = false;
            exibeAviso("O local informado é inválido","unica","erro");
            $js .= 'f.controle.value = "3" ;';
            executaFrameOculto($js);
        }

        if ($ok) {
            $bens = new bens;

            if ($bens->transferirBens($codBem,$codMasSetor,$situacao,$descSituacao)) {
                //Insere auditoria
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codBem);
                $audicao->insereAuditoria();

                //Exibe mensagem de confirmação e redireciona para o início
                alertaAviso($PHP_SELF,"Bem transferido com sucesso(Bem: ".$codBem.' - '.pegaDado("descricao","patrimonio.bem","Where cod_bem = '".$codBem."' ") .  ")","unica","aviso", "");

            } else {
                //Exibe mensagem de erro e redireciona para o início
                alertaAviso($PHP_SELF."?controle=0","Erro ao transferir bem","unica","erro");
            }
        }
    break;

/************************************************************/
/* Transfere bens EM LOTE
/************************************************************/
    case 4:
        //$html = new interfaceBens;
        //$html->formSelecionaLocal($PHP_SELF,$controle);
        $exercicio = pegaConfiguracao("ano_exercicio");

        // buscara mascara do setor
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
        $mascaraSetor = pegaConfiguracao("mascara_local");

        // operacoes no frame oculto
        switch ($ctrl_frm) {

            // preenche os combos do Local
            case 1:
                 include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosCALOLegado.inc.php';
exit();
            break;

        }
?>
        <script type="text/javascript">
            function valorMaximo(campo, limite)
            {
                if (campo.value.length > limite) // se estiver maior que o tamanho estabelecido, reduza-o
                    campo.value = campo.value.substring(0, limite);
            }

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;

                campo = document.frm.codMasSetor.value.length;
                    if (campo==0) {
                        mensagem += "@O campo local é obrigatório.";
                        erro = true;
                    }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                return !(erro);

            }// Fim da function Valida

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                if (Valida()) {
                    document.frm.target = "telaPrincipal";
                    document.frm.controle.value = "5";
                    document.frm.ctrl_frm.value = "";
                    document.frm.submit();
                }
            }

            // preenche os combos do local (Orgao, Unidade, Departamento, Setor e Local)
            function preencheLocal(variavel, valor)
            {
                document.frm.target = "oculto";
                document.frm.ctrl_frm.value = "1";
                document.frm.controle.value = "4";
                document.frm.variavel.value = variavel;
                document.frm.valor2.value = escape(valor);
                document.frm.submit();
            }
        </script>

        <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId()?>'>

            <input type="hidden" name="controle" value=''>
            <input type="hidden" name="ctrl_frm" value=''>

<?php // os hiddens abaixo sao usados para a manipulacao dos filtros CALO e NGE. ?>
            <input type="hidden" name="variavel" value=''>
            <input type="hidden" name="valor2" value=''>

        <table width="100%">
        <tr>
            <td class=alt_dados colspan=2>Informe o Local de Origem</td>
        </tr>
        <tr>
            <td class="label"  title="Informe o local em que o bem está localizado." width="30%">*Localização Atual</td>
            <td class="field">
                <input type="text" name="codMasSetor" value="<?=$codMasSetor?>" size="<?=strlen($mascaraSetor);?>" maxlength="<?=strlen($mascaraSetor);?>"
                    onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraSetor?>', this, event);"
                    onChange="JavaScript: preencheLocal( 'codMasSetor', this.value )">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o orgão do bem." width="30%">*Orgão</td>
            <td class='field'>
                <select name='codOrgao' onChange="javascript: preencheLocal('codOrgao', this.value);" style="width:400px">
                    <option value='xxx' SELECTED>Selecione</option>
<?php
                    //Faz o combo de Órgãos
                    $sSQL = "SELECT
                                cod_orgao, nom_orgao, ano_exercicio
                            FROM
                                administracao.orgao
                            ORDER
                                by nom_orgao";
                    $dbEmp = new dataBaseLegado;
                    $dbEmp->abreBD();
                    $dbEmp->abreSelecao($sSQL);
                    $dbEmp->vaiPrimeiro();

                    $comboCodOrgao = "";
                    while (!$dbEmp->eof()) {
                        $anoExercicio = trim($dbEmp->pegaCampo("ano_exercicio"));
                        $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                        $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                        $chave = $codOrgaof."-".$anoExercicio;
                        $dbEmp->vaiProximo();
                        $comboCodOrgao .= "<option value='".$chave."'";
                        if (isset($codOrgao)) {
                            if ($chave == $codOrgao) {
                                $comboCodOrgao .= " SELECTED";
                                $nomOrgao = $nomOrgaof;
                            }
                        }
                        $comboCodOrgao .= ">".$nomOrgaof." - ".$anoExercicio."</option>\n";
                    }

                    $dbEmp->limpaSelecao();
                    $dbEmp->fechaBD();
                    echo $comboCodOrgao;
?>
                </select>
                <input type="hidden" name="nomOrgao" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione a Unidade do bem." width="30%">*Unidade</td>
            <td class=field>
                <select name="codUnidade" onChange="javascript: preencheLocal('codUnidade', this.value);" style="width:400px">
                    <option value=xxx SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomUnidade" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o departamento do bem." width="30%">*Departamento</td>
            <td class="field">
                <select name="codDepartamento" onChange="javascript: preencheLocal('codDepartamento', this.value);" style="width:400px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomDepartamento" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o setor do bem." width="30%">*Setor</td>
            <td class="field">
                <select name="codSetor" onChange="javascript: preencheLocal('codSetor', this.value);" style="width:400px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomSetor" value="">
                <input type="hidden" name="anoExercicioSetor" value="">
            </td>
        </tr>
        <tr>
            <td class="label"  title="Selecione o local do bem." width="30%">*Local</td>
            <td class="field">
                <select name="codLocal" onChange="javascript: preencheLocal('descricao',this.value);" style="width:400px">
                    <option value="xxx" SELECTED>Selecione</option>
                </select>
                <input type="hidden" name="nomLocal" value="">
                <input type="hidden" name="anoExercicioLocal" value="">
            </td>
        </tr>

        <tr>
            <td colspan='2' class='field'>
                <?php geraBotaoOk(); ?>
            </td>
        </tr>
        </table>

        </form>
<?php
    break;

    case 5:

        // verifica se o local informado eh valido
        $local = explode ("/", $codMasSetor);
        if (!($vetLocal = validaLocal($local[0],$local[1]))) {
            $erro = 1;
            exibeAviso("O local informado é inválido","unica","erro");
            mudaTelaPrincipal("transfereBens.php?<?=Sessao::getId();?>controle=4");
            //$js .= 'f.controle.value = "4" ;';
            //executaFrameOculto($js);
        }

        if (!$erro) {
            // operacoes no frame oculto
            switch ($ctrl_frm) {

                // preenche os combos do Local
                case 1:
                     include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosCALOLegado.inc.php';
exit();
                break;

            }

            // buscara mascara do setor
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
            $mascaraSetor = pegaConfiguracao("mascara_local");

            $vetLocal = preg_split( "/[^a-zA-Z0-9]/", $codMasSetor);
                $codOrgao = $vetLocal[0];
                $codUnidade = $vetLocal[1];
                $codDpto = $vetLocal[2];
                $codSetor = $vetLocal[3];
                $codLocal = $vetLocal[4];
                $anoExLocal = $vetLocal[5];

                $exercicio = pegaConfiguracao("ano_exercicio");

            $sql = "SELECT
                        B.cod_bem, B.descricao,B.num_placa,H.timestamp, H.cod_situacao
                    FROM
                        patrimonio.vw_bem_ativo as B, patrimonio.historico_bem as H, patrimonio.vw_ultimo_historico as U
                    WHERE
                        U.timestamp = H.timestamp
                        And U.cod_bem = H.cod_bem
                        And H.cod_bem = B.cod_bem
                        And H.cod_local = '".$codLocal."'
                        And H.cod_setor = '".$codSetor."'
                        And H.cod_departamento = '".$codDpto."'
                        And H.cod_unidade = '".$codUnidade."'
                        And H.cod_orgao = '".$codOrgao."'
                        And H.ano_exercicio = '".$anoExLocal."'
                    ORDER by B.cod_bem ";

            $conn = new dataBaseLegado;
            $conn->abreBD();
            $conn->abreSelecao($sql);
            $conn->fechaBD();
            $conn->vaiPrimeiro();
    ?>

            <script type="text/javascript">
                function valorMaximo(campo, limite)
                {
                    if (campo.value.length > limite) // se estiver maior que o tamanho estabelecido, reduza-o
                        campo.value = campo.value.substring(0, limite);
                }

                var checkflag = "false";
                function check(field)
                {
                    if (checkflag == "false") {
                        for (i = 0; i < field.length; i++) {
                            field[i].checked = true;
                        }
                    checkflag = "true";

                    return "Limpar Todos";
                    } else {
                        for (i = 0; i < field.length; i++) {
                            field[i].checked = false;
                            }
                    checkflag = "false";

                    return "Selecionar todos"; }
                }

                function Valida()
                {
                    var mensagem = "";
                    var erro = false;
                    var campo;

                    campo = document.frm.codMasSetor.value.length;
                            if (campo==0) {
                                mensagem += "@O campo local é obrigatório.";
                                erro = true;
                            }

                    if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                    return !(erro);

                }// Fim da function Valida

                //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
                function Salvar()
                {
                    if (Valida()) {
                        document.frm.target = "telaPrincipal";
                        document.frm.controle.value = "6";
                        document.frm.ctrl_frm.value = "";
                        document.frm.submit();
                    }
                }

                // preenche os combos do local (Orgao, Unidade, Departamento, Setor e Local)
                function preencheLocal(variavel, valor)
                {
                    document.frm.target = "oculto";
                    document.frm.ctrl_frm.value = "1";
                    document.frm.controle.value = "4";
                    document.frm.variavel.value = variavel;
                    document.frm.valor2.value = escape(valor);
                    document.frm.submit();
                }
            </script>

            <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId()?>'>
                <input type="hidden" name="controle" value=''>
                <input type="hidden" name="ctrl_frm" value=''>

    <?php // os hiddens abaixo sao usados para a manipulacao dos filtros CALO e NGE. ?>
                <input type="hidden" name="variavel" value=''>
                <input type="hidden" name="valor2" value=''>

            <table width="100%">
            <tr>
                <td class="alt_dados" colspan="2">Origem</td>
            </tr>
            <tr>
                <td width="25%" title="" class="label" width="30%">Localização</td>
                <td class="field">
                  <?=$codMasSetor?>
                </td>

            </tr>
            <tr>
                <td class="label"  title="" width="30%">Orgão</td>
                <td class="field">
                  <?=$nomOrgao?>
                </td>
            </tr>
            <tr>
                <td class="label"  title="" width="30%">Unidade</td>
                <td class="field">
                  <?=$nomUnidade?>
                </td>
            </tr>
            <tr>
                <td class="label"  title="" width="30%">Departamento</td>
                <td class="field">
                  <?=$nomDepartamento?>
                </td>
            </tr>
            <tr>
                <td class="label"  title="" width="30%">Setor</td>
                <td class="field">
                  <?=$nomSetor?>
                </td>
            </tr>
            <tr>
                <td class="label"  title="" width="30%">Local</td>
                <td class="field">
                  <?=$nomLocal?>
                </td>
            </tr>
            </table>

            <table width="100%">
            <tr>
                <td class='alt_dados' colspan="5">Relação de Bens</td>
            </tr>

            <tr>
                <td class='alt_dados' width='1%'>&nbsp;</td>
                <td class='alt_dados' width='9%'>Código</td>
                <td class='alt_dados' width='9%'>Placa</td>
                <td class='alt_dados' width='46%'>Descrição</td>
                <td class='alt_dados' width='35%'>Situação</td>
            </tr>
    <?php
            while (!$conn->eof()) {
                $codBem = $conn->pegaCampo("cod_bem");
                $placa  = $conn->pegaCampo("num_placa");
                $descricao = $conn->pegaCampo("descricao");
                $situacao = $conn->pegaCampo("cod_situacao");
    ?>
                <tr>
                    <td class="fieldright" width="1%">
                        <input type="checkbox" name="codBem[<?=$codBem;?>]" id='codBem' value="<?=$codBem;?>">
                    </td>
                    <td class="field" width="9%"><?=$codBem;?></td>
                    <td class="field" width="9%"><?=$placa;?></td>
                    <td class='field' width='46%'><?=$descricao;?></td>
                    <td class="fieldcenter" >
    <?php
                    $combo = "";
                    $combo .= "<select name='situacao[$codBem]' id='situacao' style='width: 200px;>\n";

                    $combo .= "<option value='xxx'>Selecione a situação do bem</option>\n";

                    $sql = "Select cod_situacao, nom_situacao
                            From patrimonio.situacao_bem Order by nom_situacao";
                    //echo "<!--".$sql."-->";
                    $dataBase = new dataBaseLegado;
                    $dataBase->abreBD();
                    $dataBase->abreSelecao($sql);
                    $dataBase->fechaBD();
                    $dataBase->vaiPrimeiro();

                    while (!$dataBase->eof()) {
                        $codSituacao = trim($dataBase->pegaCampo("cod_situacao"));
                        $nomSituacao = trim($dataBase->pegaCampo("nom_situacao"));
                        $selected = "";
                        if ($codSituacao==$situacao) {
                            $selected = "selected";
                        }
                        $dataBase->vaiProximo();
                        $combo .=
                            "<option value='".$codSituacao."' ".$selected.">".$nomSituacao."</option>\n";
                    }

                    $dataBase->limpaSelecao();
                    $combo .= "</select>";
                    echo $combo;
    ?>
                    </td>
                </tr>
    <?php
                $conn->vaiProximo();
            }//Fim While

            //Fecha conexão com o banco de dados
            $conn->limpaSelecao();

            $codMasSetor = "";
    ?>
            <tr>
                <td colspan='4' class='field'>
                    &nbsp;<input type="button" value="Selecionar todos"
                    onClick="this.value=check(this.form.codBem)">
                </td>
            </tr>
            </table>

            <table width="100%">
            <tr>
                <td class='alt_dados' colspan="2">Destino</td>
            </tr>
            <tr>
                <td class="label"  title="Informe o local que o bem está localizado." width="30%">*Localização</td>
                <td class="field">
                    <input type="text" name="codMasSetor" value="<?=$codMasSetor?>" size="<?=strlen($mascaraSetor);?>" maxlength="<?=strlen($mascaraSetor);?>"
                        onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraSetor?>', this, event);"
                        onChange="JavaScript: preencheLocal( 'codMasSetor', this.value )">
                </td>
            </tr>
            <tr>
                <td class="label"  title="Selecione o orgão do bem." width="30%">*Orgão</td>
                <td class='field'>
                    <select name='codOrgao' onChange="javascript: preencheLocal('codOrgao', this.value);" style="width:400px">
                        <option value='xxx' SELECTED>Selecione</option>
    <?php
                        //Faz o combo de Órgãos
                        $sSQL = "SELECT
                                    cod_orgao, nom_orgao, ano_exercicio
                                FROM
                                    administracao.orgao
                                ORDER
                                    by nom_orgao";
                        $dbEmp = new dataBaseLegado;
                        $dbEmp->abreBD();
                        $dbEmp->abreSelecao($sSQL);
                        $dbEmp->vaiPrimeiro();

                        $comboCodOrgao = "";
                        while (!$dbEmp->eof()) {
                            $anoExercicio  = trim($dbEmp->pegaCampo("ano_exercicio"));
                            $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                            $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                            $chave = $codOrgaof."-".$anoExercicio;
                            $dbEmp->vaiProximo();
                            $comboCodOrgao .= "<option value='".$chave."'";
                            $comboCodOrgao .= ">".$nomOrgaof." - ".$anoExercicio."</option>\n";
                        }

                        $dbEmp->limpaSelecao();
                        $dbEmp->fechaBD();
                        echo $comboCodOrgao;
    ?>
                    </select>
                    <input type="hidden" name="nomOrgao" value="">
                </td>
            </tr>
            <tr>
                <td class="label"  title="Selecione a Unidade do bem." width="30%">*Unidade</td>
                <td class=field>
                    <select name=codUnidade onChange="javascript: preencheLocal('codUnidade', this.value);" style="width:400px">
                        <option value=xxx SELECTED>Selecione</option>
                    </select>
                    <input type="hidden" name="nomUnidade" value="">
                </td>
            </tr>
            <tr>
                <td class="label"  title="Selecione o departamento do bem." width="30%">*Departamento</td>
                <td class="field">
                    <select name="codDepartamento" onChange="javascript: preencheLocal('codDepartamento', this.value);" style="width:400px">
                        <option value="xxx" SELECTED>Selecione</option>
                    </select>
                    <input type="hidden" name="nomDepartamento" value="">
                </td>
            </tr>
            <tr>
                <td class="label"  title="Selecione o setor do bem." width="30%">*Setor</td>
                <td class="field">
                    <select name="codSetor" onChange="javascript: preencheLocal('codSetor', this.value);" style="width:400px">
                        <option value="xxx" SELECTED>Selecione</option>
                    </select>
                    <input type="hidden" name="nomSetor" value="">
                    <input type="hidden" name="anoExercicioSetor" value="">
                </td>
            </tr>
            <tr>
                <td class="label"  title="Selecione o local do bem." width="30%">*Local</td>
                <td class="field">
                    <select name="codLocal" onChange="javascript: preencheLocal('codLocal', this.value);" style="width:400px">
                        <option value="xxx" SELECTED>Selecione</option>
                    </select>
                    <input type="hidden" name="nomLocal" value="">
                    <input type="hidden" name="anoExercicioLocal" value="">
                </td>
            </tr>
            <tr>
                <td class="label" title="Informe a descrição da situação do bem.">Descrição da Situação</td>
                <td class="field">
                   <input type="text"  name="descSituacao" value="<?=$descSituacao;?>" size="80" maxlength="60" >
                </td>
            </tr>
            <tr>
                <td colspan='2' class='field'>
                    <?php geraBotaoOk(); ?>
                </td>
            </tr>
            </table>

            </form>
<?php
        }

    break;

    case 6:
        $bens = new bens;
        if ($bens->transferirBens($codBem,$codMasSetor,$situacao,$descSituacao)) {
            //Insere auditoria
            $audicao = new auditoriaLegada;

            //Insere auditoria para cada bem
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codBem);
            $audicao->insereAuditoria();

            //Exibe mensagem de confirmação e redireciona para o início
            alertaAviso($PHP_SELF,"Bens transferidos com sucesso (Bens: ".$codBem.")","unica","aviso");
        } else {
            //Exibe mensagem de erro e redireciona para o início
            alertaAviso($PHP_SELF,"Erro ao transferir bens","unica","erro");

        }
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html
?>
