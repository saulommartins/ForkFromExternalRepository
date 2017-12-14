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
  * Classe de mapeamento da tabela PESSOAL.mov_sefip_saida_categoria

  * Data de Criação: 24/02/2006

  * @author Analista: Vandre
  * @author Desenvolvedor: Bruce

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.mov_sefip_saida_categoria
  * Data de Criação: 24/02/2006

  * @author Analista: Vandre
  * @author Desenvolvedor: Bruce

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCategoriaMovimento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function TPessoalCategoriaMovimento()
{
    parent::Persistente();
    $this->setTabela('pessoal'.Sessao::getEntidade().'.mov_sefip_saida_categoria');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_sefip_saida,cod_categoria');

    $this->AddCampo('cod_categoria','integer',true,'',  true,"TPessoalCategoria");
    $this->AddCampo('cod_sefip_saida','integer',true,'',  true,"TPessoalMovSefipSaida");
    $this->AddCampo('indicativo', 'varchar',true,'1',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT mov_sefip_saida_categoria.*                                       \n";
    $stSql .= "     , categoria.descricao                                               \n";
    $stSql .= "  FROM pessoal.mov_sefip_saida_categoria         \n";
    $stSql .= "     , pessoal.categoria                         \n";
    $stSql .= " WHERE mov_sefip_saida_categoria.cod_categoria = categoria.cod_categoria \n";

    return $stSql;
}

function excluirPorMovimento($codSefipSaida,   $boTransacao = '')
{
    $obErro       = new erro;
    $obConexao    = new Conexao;
    $stSql .= $this->montaExcluirPorMovimento(). $codSefipSaida;
    $this->setDebug ( $stSql );
    $obErro       = $obConexao->executaDML ( $stSql, $boTransacao );

    return $obErro;

}//function excluirPorMovimento($boTransaca = '') {

function montaExcluirPorMovimento()
{
    $stSql = " delete from pessoal.mov_sefip_saida_categoria where cod_sefip_saida = ";

    return $stSql;

}

}