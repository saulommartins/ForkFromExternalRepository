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
    * Classe de mapeamento da tabela pessoal.forma_pagamento_ferias
    * Data de Criação: 08/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.forma_pagamento_ferias
  * Data de Criação: 08/06/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalFormaPagamentoFerias extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalFormaPagamentoFerias()
{
    parent::Persistente();
    $this->setTabela("pessoal.forma_pagamento_ferias");

    $this->setCampoCod('cod_forma');
    $this->setComplementoChave('');

    $this->AddCampo('cod_forma','integer',true,'',true,false);
    $this->AddCampo('codigo','char',true,'2',false,false);
    $this->AddCampo('dias','integer',true,'',false,false);
    $this->AddCampo('abono','integer',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT forma_pagamento_ferias.*                                                             \n";
    $stSql .= "  FROM pessoal.forma_pagamento_ferias                                                       \n";
    $stSql .= "     , pessoal.configuracao_forma_pagamento_ferias                                          \n";
    $stSql .= " WHERE forma_pagamento_ferias.cod_forma = configuracao_forma_pagamento_ferias.cod_forma     \n";

    return $stSql;
}


function recuperaDiasFeriasRestantes(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_forma ";
    $stSql  = $this->montaRecuperaDiasFeriasRestantes().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}


function montaRecuperaDiasFeriasRestantes()
{
    $stSql = "  SELECT DISTINCT 
                        forma_pagamento_ferias.*
                FROM  pessoal.forma_pagamento_ferias
                    , pessoal.configuracao_forma_pagamento_ferias
                    , pessoal.ferias
                WHERE forma_pagamento_ferias.cod_forma = configuracao_forma_pagamento_ferias.cod_forma     
                  AND ferias.cod_forma = forma_pagamento_ferias.cod_forma 
            ";
    return $stSql;
}

}//end of class
