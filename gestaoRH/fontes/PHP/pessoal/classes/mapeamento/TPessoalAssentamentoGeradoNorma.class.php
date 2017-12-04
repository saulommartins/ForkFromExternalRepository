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
    * Classe de mapeamento da tabela pessoal.assentamento_gerado_norma
    * Data de Criação: 10/01/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.04.14

    $Id: TPessoalAssentamentoGeradoNorma.class.php 63789 2015-10-13 19:21:18Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalAssentamentoGeradoNorma extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela("pessoal.assentamento_gerado_norma");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento_gerado,timestamp');

    $this->AddCampo('cod_assentamento_gerado','integer'       ,true  ,'' ,true  ,'TPessoalAssentamentoGerado');
    $this->AddCampo('timestamp'              ,'timestamp_now' ,true  ,'' ,true  ,'TPessoalAssentamentoGerado');
    $this->AddCampo('cod_norma'              ,'integer'       ,true  ,'' ,false ,'TNormasNorma');
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT assentamento_gerado_norma.*                                       \n";
    $stSql .= "     , norma.cod_tipo_norma                                              \n";
    $stSql .= "     , norma.nom_norma                                                   \n";
    $stSql .= "     , tipo_norma.nom_tipo_norma                                         \n";
    $stSql .= "  FROM pessoal.assentamento_gerado_norma       \n";
    $stSql .= "     , normas.norma                                                      \n";
    $stSql .= "     , normas.tipo_norma                                                 \n";
    $stSql .= " WHERE assentamento_gerado_norma.cod_norma = norma.cod_norma             \n";
    $stSql .= "   AND norma.cod_tipo_norma = tipo_norma.cod_tipo_norma                  \n";

    return $stSql;
}

function excluirAssentamentoGeradoNorma($stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql = $this->montaExcluirAssentamentoGeradoNorma($stFiltro);
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

function montaExcluirAssentamentoGeradoNorma($stFiltro)
{
    $stSql  = "DELETE FROM pessoal.assentamento_gerado_norma WHERE cod_assentamento_gerado IN (SELECT cod_assentamento_gerado                        \n";
    $stSql .= "                                                                                  FROM pessoal.assentamento_gerado_contrato_servidor  \n";
    $stSql .= "                                                                                 ".$stFiltro.")                                       \n";

    return $stSql;
}

}

?>