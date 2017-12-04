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
* Classe de mapeamento para administracao.variavel
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
  * Efetua conexão com a tabela  ADMINISTRACAO.VARIAVEL
  * Data de Criação: 09/08/2005

  * @author Analista: Cassiano de Vasconcellos Ferreira
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoVariavel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoVariavel()
{
    parent::Persistente();
    $this->setTabela('administracao.variavel');

    $this->setCampoCod('cod_variavel');
    $this->setComplementoChave('cod_modulo,cod_biblioteca,cod_funcao');

    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('cod_biblioteca','integer',true,'',true,true);
    $this->AddCampo('cod_funcao','integer',true,'',true,true);
    $this->AddCampo('cod_variavel','integer',true,'',true,false);
    $this->AddCampo('nom_variavel','varchar',true,'30',false,false);
    $this->AddCampo('cod_tipo','integer',true,'',false,false);
    $this->AddCampo('valor_inicial','text',true,'',false,false);

}
/**
    * Monta SQL utilizado pelo método RecuperaRelacionamento
    * @access Protected
*/
function montaRecuperaRelacionamento()
{
    $stSql .= " SELECT                                          \n";
    $stSql .= "     var.cod_modulo,                             \n";
    $stSql .= "     var.cod_biblioteca,                         \n";
    $stSql .= "     var.cod_funcao,                             \n";
    $stSql .= "     var.cod_variavel,                           \n";
    $stSql .= "     var.nom_variavel,                           \n";
    $stSql .= "     var.cod_tipo,                               \n";
    $stSql .= "     var.valor_inicial,                          \n";
    $stSql .= "     pri.nom_tipo                                \n";
    $stSql .= " FROM                                            \n";
    $stSql .= "     administracao.tipo_primitivo AS pri,        \n";
    $stSql .= "     administracao.variavel AS var               \n";
    $stSql .= " LEFT JOIN                                       \n";
    $stSql .= "     administracao.parametro AS par              \n";
    $stSql .= " ON                                              \n";
    $stSql .= "     var.cod_modulo     = par.cod_modulo     AND \n";
    $stSql .= "     var.cod_biblioteca = par.cod_biblioteca AND \n";
    $stSql .= "     var.cod_funcao     = par.cod_funcao     AND \n";
    $stSql .= "     var.cod_variavel   = par.cod_variavel       \n";
    $stSql .= " WHERE                                           \n";
    $stSql .= "     par.cod_modulo     IS NULL AND              \n";
    $stSql .= "     par.cod_biblioteca IS NULL AND              \n";
    $stSql .= "     par.cod_funcao     IS NULL AND              \n";
    $stSql .= "     par.cod_variavel   IS NULL AND              \n";
    $stSql .= "     var.cod_tipo = pri.cod_tipo                 \n";

    return $stSql;
}

}
