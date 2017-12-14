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
  * Classe de mapeamento da tabela pessoal.assentamento_gerado_contrato_servidor
  * Data de Criação: 27/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-10 13:40:16 -0300 (Seg, 10 Mar 2008) $

    Caso de uso: uc-04.04.14

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalAssentamentoGeradoContratoServidor extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPessoalAssentamentoGeradoContratoServidor()
    {
        parent::Persistente();
        $this->setTabela('pessoal.assentamento_gerado_contrato_servidor');

        $this->setCampoCod('cod_assentamento_gerado');
        $this->setComplementoChave('');

        $this->AddCampo('cod_assentamento_gerado', 'integer',  true, '',  true, false);
        $this->AddCampo('cod_contrato'           , 'integer',  true, '', false,  true);
    }

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT assentamento_gerado.*                                                                                                          \n";
    $stSql .= "     , (assentamento_gerado.periodo_final-assentamento_gerado.periodo_inicial)+1 as dias_do_periodo                                   \n";
    $stSql .= "     , assentamento_assentamento.cod_motivo                                                                                           \n";
    $stSql .= " FROM pessoal.assentamento_gerado_contrato_servidor                                                         \n";
    $stSql .= "    , pessoal.assentamento_gerado                                                                           \n";
    $stSql .= "    , (   SELECT cod_assentamento_gerado                                                                                              \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                          \n";
    $stSql .= "            FROM pessoal.assentamento_gerado                                                                \n";
    $stSql .= "        GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado                                                                  \n";
    $stSql .= "    , pessoal.assentamento                                                                                  \n";
    $stSql .= "    , (   SELECT cod_assentamento                                                                                                     \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                          \n";
    $stSql .= "            FROM pessoal.assentamento                                                                       \n";
    $stSql .= "        GROUP BY cod_assentamento) AS max_assentamento                                                                                \n";
    $stSql .= "    , pessoal.assentamento_assentamento                                                                     \n";
    $stSql .= "WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado                     \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado                                   \n";
    $stSql .= "  AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp                                                               \n";
    $stSql .= "  AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento                                                            \n";
    $stSql .= "  AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                                               \n";
    $stSql .= "  AND assentamento.timestamp = max_assentamento.timestamp                                                                             \n";
    $stSql .= "  AND assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento                                                      \n";
    $stSql .= "  AND NOT EXISTS (SELECT *                                                                                                            \n";
    $stSql .= "                    FROM pessoal.assentamento_gerado_excluido                                               \n";
    $stSql .= "                   WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado           \n";
    $stSql .= "                     AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)                                      \n";

    return $stSql;
}

function excluirAssentamentoGeradoContratoServidor($stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql = $this->montaExcluirAssentamentoGeradoContratoServidor().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

function montaExcluirAssentamentoGeradoContratoServidor()
{
    $stSql  = "DELETE FROM pessoal.assentamento_gerado_contrato_servidor \n";

    return $stSql;
}

}
