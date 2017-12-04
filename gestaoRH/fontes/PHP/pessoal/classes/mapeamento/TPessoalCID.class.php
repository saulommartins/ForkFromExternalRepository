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
  * Classe de mapeamento da tabela PESSOAL.CID
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.38
               uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CID
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCid extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCid()
{
    parent::Persistente();
    $this->setTabela('pessoal.cid');

    $this->setCampoCod('cod_cid');
    $this->setComplementoChave('');

    $this->AddCampo('cod_cid'              , 'integer', true,   '',  true, false);
    $this->AddCampo('sigla'                , 'char'   , true,   '', false, false);
    $this->AddCampo('descricao'            , 'varchar', true, '80', false, false);
    $this->AddCampo('cod_tipo_deficiencia' , 'integer', true,   '', false, 'TPessoalTipoDeficiencia');
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT cid.* \n";
    $stSql .= "     , tipo_deficiencia.num_deficiencia \n";
    $stSql .= "     , tipo_deficiencia.descricao as descricao_deficiencia \n";
    $stSql .= "  FROM pessoal.cid \n";
    $stSql .= "     , pessoal.tipo_deficiencia\n";
    $stSql .= " WHERE cid.cod_tipo_deficiencia = tipo_deficiencia.cod_tipo_deficiencia\n";

    return $stSql;
}

}
