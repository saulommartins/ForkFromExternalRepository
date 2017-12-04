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
    * Classe de mapeamento da tabela ALMOXARIFADO.CATALOGO
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.04 uc-03.03.05
*/

/*
$Log$
Revision 1.16  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.15  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.CATALOGO
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoCatalogo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoCatalogo()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.catalogo');

    $this->setCampoCod('cod_catalogo');
    $this->setComplementoChave('');

    $this->AddCampo('cod_catalogo','integer',true,'',true,false);
    $this->AddCampo('descricao','varchar',true,'160',false,false);
    $this->AddCampo('permite_manutencao','boolean',false,'',false,false);
    ## $this->AddCampo('importado','boolean',true,'1',false,false);
}

function montaRecuperaRelacionamento()
{

    $stSql  = "SELECT \n";
    $stSql .= "     cod_catalogo \n";
    $stSql .= "    ,descricao\n";
    $stSql .= "    ,publico.concatenar_ponto(masc) as mascara       \n";
    $stSql .= "FROM (                                               \n";
    $stSql .= "    SELECT                                           \n";
    $stSql .= "               c.cod_catalogo     as cod_catalogo,   \n";
    $stSql .= "               c.descricao        as descricao,      \n";
    $stSql .= "               n.mascara          as masc,           \n";
    $stSql .= "               n.nivel                               \n";
    $stSql .= "    FROM                                             \n";
    $stSql .= "               almoxarifado.catalogo as c            \n";
    $stSql .= "    LEFT JOIN  almoxarifado.catalogo_niveis n ON     \n";
    $stSql .= "               c.cod_catalogo = n.cod_catalogo       \n";
    $stSql .= "    WHERE                                            \n";
    $stSql .= "               c.permite_manutencao = true           \n";

    if ($this->getDado('stDescricao')) {
          $stSql .= "    AND  c.descricao  ilike '". $this->getDado('stDescricao') . "'";
    }

    if ($this->getDado('boNaoExcluiveis')) {
        $stSql .= "    AND c.cod_catalogo NOT IN (select cod_catalogo from almoxarifado.catalogo_classificacao) \n";
    }

    $stSql .= "    GROUP BY c.cod_catalogo, c.descricao, n.mascara, n.nivel\n";
    $stSql .= "    ORDER BY c.cod_catalogo, n.nivel\n";
    $stSql .= ") as tabela\n";
    $stSql .= "GROUP BY cod_catalogo, descricao\n";
    $stSql .= "ORDER BY cod_catalogo\n";

    return $stSql;
}

function atualizaCodigoEstrutural($boTransacao='')
{
    $stFiltro = '';
    $obErro     = new Erro;
    $obConexao  = new Conexao;
    $this->setDebug( 'alteracao' );

    if ($this->getDado("codCatalogo")) {
        $stFiltro .= ' cod_catalogo = ' . $this->getDado("codCatalogo");

        if ($this->getDado("codClassificacao")) {
            $stFiltro .= ' AND cod_classificacao = ' . $this->getDado("codClassificacao");
        }

        $stFiltro = ' WHERE '.$stFiltro;
    }

    $stSql = "UPDATE almoxarifado.catalogo_classificacao SET cod_estrutural='".$this->getDado('cod_estrutural')."' " . $stFiltro;

    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

}
