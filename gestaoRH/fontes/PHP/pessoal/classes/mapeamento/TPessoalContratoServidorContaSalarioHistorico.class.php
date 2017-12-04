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
  * Classe de mapeamento da tabela pessoal.contrato_servidor_conta_salario_historico
  * Data de Criação: 15/04/2014

  * @author Analista: Dagiane
  * @author Desenvolvedor: Carlos Adriano

  * @package URBEM
  * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalContratoServidorContaSalarioHistorico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorContaSalarioHistorico()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_conta_salario_historico');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato, timestamp');

    $this->AddCampo('cod_contrato', 'integer'   , true, ''   , true , true);
    $this->AddCampo('timestamp'   , 'timestamp' , false, ''  , true , false);
    $this->AddCampo('cod_agencia' , 'integer'   , true, ''   , false, true);
    $this->AddCampo('cod_banco'   , 'integer'   , false, ''  , false, false);
    $this->AddCampo('nr_conta'    , 'char'      , false, '15', false, false);
}

function excluirContratoServidorContaSalarioHistorico($stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql = $this->montaExcluirContratoServidorContaSalarioHistorico().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecord, $stSql, $boTransacao );

    return $obErro;
}

function montaExcluirContratoServidorContaSalarioHistorico()
{
    $stSql = "DELETE FROM pessoal.contrato_servidor_conta_salario_historico ";

    return $stSql;
}

}