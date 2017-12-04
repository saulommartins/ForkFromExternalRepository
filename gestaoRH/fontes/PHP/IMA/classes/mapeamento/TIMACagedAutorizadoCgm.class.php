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
    * Classe de mapeamento da tabela ima.caged_autorizado_cgm
    * Data de Criação: 18/04/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-tabelas

    $Id: TIMACagedAutorizadoCgm.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.caged_autorizado_cgm
  * Data de Criação: 18/04/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMACagedAutorizadoCgm extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMACagedAutorizadoCgm()
{
    parent::Persistente();
    $this->setTabela("ima.caged_autorizado_cgm");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_configuracao');

    $this->AddCampo('cod_configuracao','integer',true  ,''   ,true,'TIMAConfiguracaoCaged');
    $this->AddCampo('numcgm'          ,'integer',true  ,''   ,false,'TCGM');
    $this->AddCampo('num_autorizacao' ,'char'   ,true  ,'7'  ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT caged_autorizado_cgm.*                             \n";
    $stSql .= "     , sw_cgm.nom_cgm                                     \n";
    $stSql .= "     , (SELECT cnpj FROM sw_cgm_pessoa_juridica WHERE numcgm = caged_autorizado_cgm.numcgm) as cnpj\n";
    $stSql .= "  FROM ima.caged_autorizado_cgm \n";
    $stSql .= "     , sw_cgm                                             \n";
    $stSql .= " WHERE caged_autorizado_cgm.numcgm = sw_cgm.numcgm        \n";

    return $stSql;
}

}
?>
