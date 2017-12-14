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
    * Classe de mapeamento da tabela pessoal.contrato_pensionista_conta_salario
    * Data de Criação: 15/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.contrato_pensionista_conta_salario
  * Data de Criação: 15/08/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoPensionistaContaSalario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoPensionistaContaSalario()
{
    parent::Persistente();
    $this->setTabela("pessoal.contrato_pensionista_conta_salario");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,timestamp');

    $this->AddCampo('cod_contrato','integer',true,'',true,"TPessoalContratoPensionista");
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('cod_banco','integer',true,'',false,"TMONAgencia");
    $this->AddCampo('cod_agencia','integer',true,'',false,"TMONAgencia");
    $this->AddCampo('nr_conta','varchar',false,'11',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT contrato_pensionista_conta_salario.*                                                      \n";
    $stSql .= "     , agencia.num_agencia                                                                       \n";
    $stSql .= "     , banco.num_banco                                                                           \n";
    $stSql .= "  FROM pessoal.contrato_pensionista_conta_salario                                                \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                    \n";
    $stSql .= "               , max(timestamp) as timestamp                                                     \n";
    $stSql .= "            FROM pessoal.contrato_pensionista_conta_salario                                      \n";
    $stSql .= "        GROUP BY  cod_contrato) as max_contrato_pensionista_conta_salario                        \n";
    $stSql .= "     , monetario.agencia                                                                         \n";
    $stSql .= "     , monetario.banco                                                                           \n";
    $stSql .= " WHERE contrato_pensionista_conta_salario.cod_contrato = max_contrato_pensionista_conta_salario.cod_contrato    \n";
    $stSql .= "   AND contrato_pensionista_conta_salario.timestamp = max_contrato_pensionista_conta_salario.timestamp          \n";
    $stSql .= "   AND contrato_pensionista_conta_salario.cod_agencia = agencia.cod_agencia                      \n";
    $stSql .= "   AND contrato_pensionista_conta_salario.cod_banco = agencia.cod_banco                          \n";
    $stSql .= "   AND contrato_pensionista_conta_salario.cod_banco = banco.cod_banco                            \n";

    return $stSql;
}

}
