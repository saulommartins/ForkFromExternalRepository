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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.CONFIGURACAO_EVENTO_DESPESA
    * Data de Criação: 26/08/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Antunez

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-24 16:42:34 -0300 (Ter, 24 Jul 2007) $

    * Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.CONFIGURACAO_EVENTO_DESPESA
  * Data de Criação: 26/08/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Antunez

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoEventoDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoConfiguracaoEventoDespesa()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.configuracao_evento_despesa');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_evento,timestamp,cod_configuracao');

    $this->AddCampo('cod_evento','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',true,'',true,true);
    $this->AddCampo('cod_configuracao','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',false,true);
    $this->AddCampo('cod_conta','integer',true,'',false,true);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT configuracao_evento_despesa.*                                         \n";
    $stSql .= "     , conta_despesa.cod_estrutural                                          \n";
    $stSql .= "     , configuracao_evento_caso.descricao                                    \n";
    $stSql .= "     , configuracao_evento_caso.cod_caso                                     \n";
    $stSql .= "     , configuracao_evento_caso.cod_configuracao                             \n";
    $stSql .= "     , funcao.nom_funcao                                                     \n";
    $stSql .= "  FROM folhapagamento.configuracao_evento_despesa                            \n";
    $stSql .= "     , folhapagamento.evento_configuracao_evento                             \n";
    $stSql .= "     , folhapagamento.configuracao_evento_caso                               \n";
    $stSql .= "     , orcamento.conta_despesa                                               \n";
    $stSql .= "     , administracao.funcao                                                  \n";
    $stSql .= " WHERE configuracao_evento_despesa.cod_conta = conta_despesa.cod_conta     \n";
    $stSql .= "   AND configuracao_evento_despesa.exercicio   = conta_despesa.exercicio     \n";
    $stSql .= "   AND configuracao_evento_despesa.cod_evento  = evento_configuracao_evento.cod_evento   \n";
    $stSql .= "   AND configuracao_evento_despesa.timestamp   = evento_configuracao_evento.timestamp    \n";
    $stSql .= "   AND configuracao_evento_despesa.cod_configuracao = evento_configuracao_evento.cod_configuracao    \n";
    $stSql .= "   AND evento_configuracao_evento.cod_evento   = configuracao_evento_caso.cod_evento     \n";
    $stSql .= "   AND evento_configuracao_evento.timestamp    = configuracao_evento_caso.timestamp      \n";
    $stSql .= "   AND evento_configuracao_evento.cod_configuracao = configuracao_evento_caso.cod_configuracao       \n";
    $stSql .= "   AND configuracao_evento_caso.cod_modulo     = funcao.cod_modulo           \n";
    $stSql .= "   AND configuracao_evento_caso.cod_biblioteca = funcao.cod_biblioteca       \n";
    $stSql .= "   AND configuracao_evento_caso.cod_funcao     = funcao.cod_funcao           \n";

    return $stSql;
}

}
