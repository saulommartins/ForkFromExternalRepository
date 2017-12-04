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
  * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_NIVEL_PADRAO
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
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_NIVEL_PADRAO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorNivelPadrao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorNivelPadrao()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_nivel_padrao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_nivel_padrao,timestamp');

    $this->AddCampo('cod_contrato'          ,'integer'  ,true,''    ,true,true);
    $this->AddCampo('cod_nivel_padrao'      ,'integer'  ,true,''    ,true,true);
    $this->AddCampo('timestamp'             ,'timestamp',false,''   ,true,false);
    $this->AddCampo('cod_periodo_movimentacao','integer',true,''    ,false,true);
    $this->AddCampo('reajuste'              ,'boolean'  ,false,''   ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT                                                   \n";
    $stSql .= "     fn.*                                                \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "     pessoal.contrato_servidor_nivel_padrao as pp,   \n";
    $stSql .= "     folhapagamento.nivel_padrao            as fp,   \n";
    $stSql .= "     folhapagamento.nivel_padrao_nivel      as fn    \n";
    $stSql .= "WHERE                                                    \n";
    $stSql .= "     pp.cod_nivel_padrao = fp.cod_nivel_padrao           \n";
    $stSql .= "     AND fn.cod_nivel_padrao = fp.cod_nivel_padrao       \n";

    return $stSql;
}
}
