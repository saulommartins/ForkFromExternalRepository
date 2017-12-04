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
  * Classe de mapeamento da tabela pessoal.assentamento_gerado_excluido
  * Data de Criação: 10/05/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.14

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalAssentamentoGeradoExcluido extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoGeradoExcluido()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_gerado_excluido');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento_gerado,timestamp');

    $this->AddCampo('cod_assentamento_gerado' , 'integer'   ,  true, '',  true,  true);
    $this->AddCampo('timestamp'               , 'timestamp' , false, '',  true, true);
    $this->AddCampo('timestamp_excluido'      , 'timestamp' , false, '',  false, false);
    $this->AddCampo('descricao'               , 'char'      ,  true, '200', false, false);
}
function excluirAssentamentoGeradoExcluido($stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql = $this->montaExcluirAssentamentoGeradoExcluido($stFiltro);
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

function montaExcluirAssentamentoGeradoExcluido($stFiltro)
{
    $stSql  = "DELETE FROM pessoal.assentamento_gerado_excluido WHERE cod_assentamento_gerado IN (SELECT cod_assentamento_gerado                        \n";
    $stSql .= "                                                                                     FROM pessoal.assentamento_gerado_contrato_servidor  \n";
    $stSql .= "                                                                                    ".$stFiltro.")                                       \n";

    return $stSql;
}

}
