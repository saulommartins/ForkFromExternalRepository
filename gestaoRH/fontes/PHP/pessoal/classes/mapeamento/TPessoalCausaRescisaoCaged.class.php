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
    * Classe de mapeamento da tabela pessoal.causa_rescisao_caged
    * Data de Criação: 23/04/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-tabelas

    $Id: TPessoalCausaRescisaoCaged.class.php 30566 2008-06-27 13:50:23Z domluc $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.causa_rescisao_caged
  * Data de Criação: 23/04/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCausaRescisaoCaged extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCausaRescisaoCaged()
{
    parent::Persistente();
    $this->setTabela("pessoal.causa_rescisao_caged");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_causa_rescisao,cod_caged');

    $this->AddCampo('cod_causa_rescisao','integer',true  ,'',true,'TPessoalCausaRescisao');
    $this->AddCampo('cod_caged'         ,'integer',true  ,'',true,'TPessoalCaged');

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT causa_rescisao_caged.*                                            \n";
    $stSql .= "     , caged.num_caged                                      \n";
    $stSql .= "     , caged.descricao                                      \n";
    $stSql .= "  FROM pessoal.causa_rescisao_caged            \n";
    $stSql .= "     , pessoal.caged              \n";
    $stSql .= " WHERE causa_rescisao_caged.cod_caged = caged.cod_caged     \n";

    return $stSql;
}

}
?>
