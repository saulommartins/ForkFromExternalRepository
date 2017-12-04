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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

    $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
    //echo $mascaraAssunto;
?>
    <script type="text/javascript">
        function preenche_combos(campo_a, campo_b)
        {
            document.frm.action = "<?=$action;?>?<?=Sessao::getId()?>&controle=<?=$ctrl;?>";
            document.frm.target = 'telaPrincipal';
            document.frm.change_ClassifAssunto2.value = '1';
            document.frm.submit();
            preencheCA (campo_a, campo_b);
        }

        function verifica_valores()
        {
            if ((document.frm.codClassificacao.value != 'xxx') && (document.frm.codAssunto.value != 'xxx')) {
                document.frm.action = "<?=$action;?>?<?=Sessao::getId()?>&controle=<?=$ctrl;?>";
                document.frm.target = 'telaPrincipal';
                document.frm.change_ClassifAssunto.value = '1';
                document.frm.submit();
            }
        }
    </script>
<?php
        // se codAssunto e codClassficacao estado setados...
        // monta a variavel $codClassifAssunto
        if ($codAssunto and $codClassificacao and
            $codClassificacao != 'xxx' and $codAssunto != 'xxx' and
            $change_ClassifAssunto == 1
          ) {
            $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $codClassificacao."-".$codAssunto);
            $codClassifAssunto   = $arCodClassifAssunto[1];
        }

        // quando a operacao for inclusao de processo utiliza a verificaca de valores para submeter o form
        if (Sessao::read('acao') == 57) {
            $submit = "verifica_valores()";
        } else {
            $submit = "";
        }
?>
    <tr>
        <td class=label width=30% rowspan=3 title="Classificação e assunto de processo">Classificação/Assunto</td>
        <td class=field>
            <input type="hidden" size="2" name="change_ClassifAssunto" value="">
            <input type="hidden" size="2" name="change_ClassifAssunto2" value="">

            <input type="text" name="codClassifAssunto" size="<?=strlen($mascaraAssunto);?>" maxlength="<?=strlen($mascaraAssunto);?>" value="<?=$codClassifAssunto?>"
                onKeyUp="JavaScript: mascaraDinamico('<?=$mascaraAssunto?>', this, event);"
                onChange="JavaScript:preenche_combos( 'codClassifAssunto', this.value );">
        </td>
    </tr>
    <tr>
        <td class=field>
        <select name="codClassificacao" onChange="JavaScript: preencheCA( 'codClassificacao', this.value );<?=$submit;?>" style="width: 200px">
            <option value="xxx">Selecione classificação</option>
<?php
            $sSQL = "SELECT * FROM sw_classificacao ORDER by nom_classificacao";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboCla = "";
            while (!$dbEmp->eof()) {
            $codClassificacaof  = trim($dbEmp->pegaCampo("cod_classificacao"));
            $nomClassificacaof  = trim($dbEmp->pegaCampo("nom_classificacao"));
            $dbEmp->vaiProximo();
            $comboCla .= "         <option value=".$codClassificacaof;
            if (isset($codClassificacao)) {
                if ($codClassificacaof == $codClassificacao)
                    $comboCla .= " SELECTED";
            }
            $comboCla .= ">".$nomClassificacaof."</option>\n";
            }
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
            echo $comboCla;
?>
        </select>
        </td>
    </tr>
    <tr>
        <td class=field>

        <select name="codAssunto" onChange="JavaScript: preencheCA( 'codAssunto', this.value ); <?=$submit;?>" style="width: 200px">
            <option value="xxx" SELECTED>Selecione assunto</option>
    <?php
    if ((isset($codClassificacao)) AND ($codClassificacao != "xxx")) {
    $sSQL = "SELECT * FROM sw_assunto WHERE cod_classificacao = ".$codClassificacao." ORDER by nom_assunto";
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $comboAss = "";
    while (!$dbEmp->eof()) {
        $codAssuntof  = trim($dbEmp->pegaCampo("cod_assunto"));
        $nomAssuntof  = trim($dbEmp->pegaCampo("nom_assunto"));
        $dbEmp->vaiProximo();
        $comboAss .= "         <option value=".$codAssuntof;
        if (isset($codAssunto)) {
            if ($codAssuntof == $codAssunto)
                $comboAss .= " SELECTED";
        }
        $comboAss .= ">".$nomAssuntof."</option>\n";
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $comboAss;
    }
    ?>
        </select>
        </td>
    </tr>
    <tr>
        <td class=label width=30% title="Descrição rápida do assunto do processo">Assunto reduzido</td>
        <td class=field>
            <input type="text" name="resumo" size="80" value="<?=$resumo?>">
        </td>
    </tr>
