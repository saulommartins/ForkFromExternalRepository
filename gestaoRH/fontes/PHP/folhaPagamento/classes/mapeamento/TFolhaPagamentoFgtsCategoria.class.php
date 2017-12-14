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
    * Classe de mapeamento da tabela folhapagamento.fgts_categoria
    * Data de Criação: 10/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.fgts_categoria
  * Data de Criação: 10/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoFgtsCategoria extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoFgtsCategoria()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.fgts_categoria");

    $this->setCampoCod('');
    $this->setComplementoChave('timestamp,cod_categoria,cod_fgts');

    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('cod_categoria','integer',true,'',true,true);
    $this->AddCampo('cod_fgts','integer',true,'',true,true);
    $this->AddCampo('aliquota_deposito','numeric',true,'5,2',false,false);
    $this->AddCampo('aliquota_contribuicao','numeric',true,'5,2',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT fgts_categoria.*                                            \n";
    $stSql .= "     , categoria.descricao                                         \n";
    $stSql .= "  FROM folhapagamento.fgts_categoria                               \n";
    $stSql .= "     , (  SELECT cod_fgts                                          \n";
    $stSql .= "               , max(timestamp) as timestamp                       \n";
    $stSql .= "            FROM folhapagamento.fgts_categoria                     \n";
    $stSql .= "        GROUP BY cod_fgts) as max_fgts_categoria                   \n";
    $stSql .= "     , pessoal.categoria                                           \n";
    $stSql .= " WHERE fgts_categoria.cod_categoria = categoria.cod_categoria      \n";
    $stSql .= "   AND fgts_categoria.cod_fgts   = max_fgts_categoria.cod_fgts     \n";
    $stSql .= "   AND fgts_categoria.timestamp  = max_fgts_categoria.timestamp    \n";

    return $stSql;
}

}
