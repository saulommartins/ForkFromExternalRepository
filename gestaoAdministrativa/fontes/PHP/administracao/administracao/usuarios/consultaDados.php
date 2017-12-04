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

    $Id: consultaDados.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO.'usuarioLegado.class.php';
include_once CAM_FW_LEGADO.'paginacaoLegada.class.php';
include_once CAM_FW_LEGADO.'auditoriaLegada.class.php';
include_once CAM_FW_LEGADO.'mascarasLegado.lib.php';
include_once CAM_FW_LEGADO.'funcoesLegado.lib.php';
include_once CAM_FW_LEGADO.'dataBaseLegado.class.php';
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once 'interfaceUsuario.class.php';

setAjuda("UC-01.03.93");

$usuario = new usuarioLegado;
if ($dadosUsuario = $usuario->pegaDadosUsuario(Sessao::read('numCgm'))) {

    if (is_array($dadosUsuario)) {
        //Grava como variável o nome da chave do vetor com o seu respectivo valor
        foreach ($dadosUsuario as $campo=>$valor) {
            $$campo = trim($valor);
        }
    }

    $catHabilitacao = pegaDado("cod_categoria_cnh", "sw_cgm_pessoa_fisica", "where numcgm = '".Sessao::read('numCgm')."'");

    if ($catHabilitacao != "") {
        $catHabilitacao = pegaDado("nom_categoria", "sw_categoria_habilitacao"," where cod_categoria = '".$catHabilitacao."' ");
    }

?>
<table width='100%'>
    <tr><td class=alt_dados colspan=2>Informações do Usuário</td></tr>
    <tr>
        <td class="label" width='20%'>CGM:</td>
        <td class="field" width='80%'><?=$numCgm;?></td>
    </tr>
    <tr>
        <td class="label">Nome</td>
        <td class="field"><?=$nomCgm;?></td>
    </tr>
    <?php if ($pessoa=='fisica') { ?>
    <tr>
        <td class="label">CPF</td>
        <td class="field"><?php echo numeroToCpf($cpf); ?></td>
    </tr>
    <tr>
        <td class="label">RG</td>
        <td class="field"><?=$rg;?></td>
    </tr>
    <tr>
        <td class="label">Órgão emissor</td>
        <td class="field"><?=$orgaoEmissor;?></td>
    </tr>
    <tr>
        <td class="label">Data de emissão</td>
        <td class="field"><?=$dtEmissaoRg;?></td>
    </tr>
    <tr>
        <td class="label">Número CNH</td>
        <td class="field"><?=$numCnh;?></td>
    </tr>
    <tr>
        <td class="label">Categoria de habilitação</td>
        <td class="field"><?=$catHabilitacao;?></td>
    </tr>
    <tr>
        <td class="label">Data de validade da CNH</td>
        <td class="field"><?=$dtValidadeCnh?></td>
    </tr>

    <?php } elseif ($pessoa=='juridica') { ?>
    <tr>
        <td class="label">CNPJ</td>
        <td class="field"><?php echo numeroToCnpj($cnpj); ?></td>
    </tr>
    <tr>
        <td class="label">Inscrição Estadual</td>
        <td class="field"><?=$inscEst;?></td>
    </tr>
    <?php } ?>
    <tr><td class=alt_dados colspan=2>Dados de Endereço</td></tr>
    <tr>
        <td class="label">Endereço</td>
        <td class="field"><?=$endereco;?></td>
    </tr>
    <tr>
        <td class="label">Estado</td>
        <td class="field"><?=$estado;?></td>
    </tr>
    <tr>
        <td class="label">Cidade</td>
        <td class="field"><?=$municipio;?></td>
    </tr>
    <tr>
        <td class="label">Bairro</td>
        <td class="field"><?=$bairro;?></td>
    </tr>
    <tr>
        <td class="label">CEP</td>
        <td class="field"><?php echo formataCep($cep); ?></td>
    </tr>
    <tr><td class=alt_dados colspan=2>Dados de Endereço para Correspondência</td></tr>
    <tr>
        <td class="label">Endereço</td>
        <td class="field"><?=$enderecoCorresp;?></td>
    </tr>
    <tr>
        <td class="label">Estado</td>
        <td class="field"><?=$estadoCorresp;?></td>
    </tr>
    <tr>
        <td class="label">Cidade</td>
        <td class="field"><?=$municipioCorresp;?></td>
    </tr>
    <tr>
        <td class="label">Bairro</td>
        <td class="field"><?=$bairroCorresp;?></td>
    </tr>
    <tr>
        <td class="label">CEP</td>
        <td class="field"><?php echo formataCep($cepCorresp); ?></td>
    </tr>
    <tr><td class=alt_dados colspan=2>Dados para Contato</td></tr>
    <tr>
        <td class="label">Telefone Residencial</td>
        <td class="field"><?php echo formataFone($foneRes); ?></td>
    </tr>
    <tr>
        <td class="label">Telefone Comercial</td>
        <td class="field"><?php echo formataFone($foneCom); ?></td>
    </tr>
    <tr>
        <td class="label">Telefone Celular</td>
        <td class="field"><?php echo formataFone($foneCel); ?></td>
    </tr>
    <tr>
        <td class="label">e-mail</td>
        <td class="field"><?=$email;?></td>
    </tr>
    <tr>
        <td class="label">e-mail adicional</td>
        <td class="field"><?=$emailAdic;?></td>
    </tr>
    <tr><td class=alt_dados colspan=2>Dados de Usuário</td></tr>
    <tr>
        <td class="label">Username</td>
        <td class="field"><?=$username;?></td>
    </tr>
    <tr>
        <td class="label">Status</td>
        <td class="field"><?=$status;?></td>
    </tr>
</table>
<?php

    # Cria um formulário para o componente do Organograma.
    $obFormulario = new Formulario;

    # Instancia do novo componente de Organograma.
    $obIMontaOrganograma = new IMontaOrganograma;
    $obIMontaOrganograma->setComponenteSomenteLeitura(true);
    $obIMontaOrganograma->setCodOrgao($codOrgao);
    $obIMontaOrganograma->geraFormulario($obFormulario);

    $obFormulario->montaHTML();
    echo $obFormulario->getHTML();

}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
