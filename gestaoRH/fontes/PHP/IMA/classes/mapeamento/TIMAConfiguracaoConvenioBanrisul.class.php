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
    * Classe de mapeamento da tabela ima.configuracao_convenio_banrisul
    * Data de Criação: 26/02/2008

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TIMAConfiguracaoConvenioBanrisul.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.configuracao_convenio_banrisul_banrisul
  * Data de Criação: 09/10/2007

  * @author Analista: Dagiane
  * @author Desenvolvedor: Alex Cardoso

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAConfiguracaoConvenioBanrisul extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAConfiguracaoConvenioBanrisul()
{
    parent::Persistente();
    $this->setTabela("ima.configuracao_convenio_banrisul");

    $this->setCampoCod('cod_convenio');
    $this->setComplementoChave('');

    $this->AddCampo('cod_convenio'      ,'sequence',true  ,''    ,true,false);
    $this->AddCampo('cod_convenio_banco','varchar' ,true  ,'20'  ,false,false);
    $this->AddCampo('cod_banco'         ,'integer' ,true  ,''    ,true,'TMONBanco');

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT configuracao_convenio_banrisul.*                                                 \n";
    $stSql .= "     , configuracao_convenio_banrisul_conta.*                                           \n";
    $stSql .= "     , conta_corrente.num_conta_corrente                                                \n";
    $stSql .= "     , agencia.num_agencia                                                              \n";
    $stSql .= "     , agencia.nom_agencia                                                              \n";
    $stSql .= "     , banco.num_banco                                                                  \n";
    $stSql .= "     , banco.nom_banco                                                                  \n";
    $stSql .= "  FROM ima.configuracao_convenio_banrisul                                               \n";
    $stSql .= "     , monetario.conta_corrente                                                         \n";
    $stSql .= "     , monetario.agencia                                                                \n";
    $stSql .= "     , monetario.banco                                                                  \n";
    $stSql .= " WHERE configuracao_convenio_banrisul.cod_convenio = configuracao_convenio_banrisul_conta.cod_convenio \n";
    $stSql .= "   AND configuracao_convenio_banrisul.cod_banco = configuracao_convenio_banrisul_conta.cod_banco       \n";
    $stSql .= "   AND configuracao_convenio_banrisul_conta.cod_conta_corrente = conta_corrente.cod_conta_corrente     \n";
    $stSql .= "   AND configuracao_convenio_banrisul_conta.cod_agencia = conta_corrente.cod_agencia                   \n";
    $stSql .= "   AND configuracao_convenio_banrisul_conta.cod_banco   = conta_corrente.cod_banco                     \n";
    $stSql .= "   AND conta_corrente.cod_agencia = agencia.cod_agencia                                 \n";
    $stSql .= "   AND conta_corrente.cod_banco   = agencia.cod_banco                                   \n";
    $stSql .= "   AND agencia.cod_banco = banco.cod_banco                                              \n";

    return $stSql;
}

}
?>
