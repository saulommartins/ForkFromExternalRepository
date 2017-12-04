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
  * Classe de mapeamento da tabela PESSOAL.CTPS
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CTPS
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCTPS extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCTPS()
{
    parent::Persistente();
    $this->setTabela('pessoal.ctps');

    $this->setCampoCod('cod_ctps');
    $this->setComplementoChave('');

    $this->AddCampo('cod_ctps','integer',true,'',true,false);
    $this->AddCampo('numero','char',true,'10',false,false);
    $this->AddCampo('dt_emissao','date',true,'',false,false);
    $this->AddCampo('orgao_expedidor','char',true,'10',false,false);
    $this->AddCampo('serie','char',true,'5',false,false);
    $this->AddCampo('uf_expedicao','integer',true,'',false,true);
}

function montaRecuperaRelacionamento()
{
$stSql  = " select                                                    \n";
$stSql .= "        c.cod_ctps                                         \n";
$stSql .= "      , c.numero                                           \n";
$stSql .= "      , to_char(c.dt_emissao, 'dd/mm/yyyy') as dt_emissao  \n";
$stSql .= "      , c.orgao_expedidor                                  \n";
$stSql .= "      , c.serie                                            \n";
$stSql .= "      , c.uf_expedicao                                     \n";
$stSql .= "      , (SELECT sigla_uf from sw_uf where sw_uf.cod_uf = c.uf_expedicao) as sigla \n";
$stSql .= " from                                                      \n";
$stSql .= "        pessoal.servidor_ctps as sc                        \n";
$stSql .= "      , pessoal.ctps as c                                  \n";
$stSql .= " where                                                     \n";
$stSql .= "        sc.cod_ctps = c.cod_ctps                           \n";

return $stSql;
}
}
