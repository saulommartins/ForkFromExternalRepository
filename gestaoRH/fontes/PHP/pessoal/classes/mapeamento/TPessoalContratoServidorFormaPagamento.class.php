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
    * Classe de mapeamento da tabela pessoal.contrato_servidor_forma_pagamento
    * Data de Criação: 30/04/2009

    * @author Analista     : Dagiane
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalContratoServidorFormaPagamento extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TPessoalContratoServidorFormaPagamento()
    {
        parent::Persistente();
        $this->setTabela("pessoal.contrato_servidor_forma_pagamento");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_contrato,timestamp');

        $this->AddCampo('cod_contrato'       ,'integer'      ,true  ,'',true,'TPessoalContratoServidor');
        $this->AddCampo('timestamp'          ,'timestamp_now',true  ,'',true,false);
        $this->AddCampo('cod_forma_pagamento','integer'      ,true  ,'',false,'TPessoalFormaPagamento');
    }

    public function montaRecuperaUltimaFormaPagamento()
    {
        $stSql  = "        SELECT contrato_servidor_forma_pagamento.*                                                                 \n";
        $stSql .= "          FROM pessoal.contrato_servidor_forma_pagamento                                                           \n";
        $stSql .= "    INNER JOIN (  SELECT contrato_servidor_forma_pagamento.cod_contrato                                            \n";
        $stSql .= "                       , max(timestamp) as timestamp                                                               \n";
        $stSql .= "                    FROM pessoal.contrato_servidor_forma_pagamento                                                 \n";
        $stSql .= "                GROUP BY contrato_servidor_forma_pagamento.cod_contrato                                            \n";
        $stSql .= "               ) as max_contrato_servidor_forma_pagamento                                                          \n";
        $stSql .= "            ON contrato_servidor_forma_pagamento.cod_contrato = max_contrato_servidor_forma_pagamento.cod_contrato \n";
        $stSql .= "           AND contrato_servidor_forma_pagamento.timestamp = max_contrato_servidor_forma_pagamento.timestamp       \n";

        return $stSql;
    }

    public function recuperaUltimaFormaPagamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro = $this->executaRecupera("montaRecuperaUltimaFormaPagamento",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $obErro;
    }
}
?>
