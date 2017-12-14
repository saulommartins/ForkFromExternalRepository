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

/*
    * Classe de mapeamento da tabela tcmgo.de_para_tipo_retencao
    * Data de Criação   : 06/04/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGODeParaTipoRetencao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGODeParaTipoRetencao()
    {
        parent::Persistente();

        $this->setTabela("tcmgo.de_para_tipo_retencao");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio_tipo, cod_tipo, exercicio, cod_plano');

        $this->AddCampo( 'exercicio_tipo'      , 'integer' , true  , ''      , true  , true );
        $this->AddCampo( 'cod_tipo'            , 'integer' , true  , ''      , true  , true );
        $this->AddCampo( 'exercicio'           , 'varchar' , true  , ''      , true  , true );
        $this->AddCampo( 'cod_plano'           , 'integer' , true  , ''      , true  , true );
    }

    public function listRetencaoReceita(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT ordem_pagamento_retencao.cod_plano
                 , SUBSTRING(plano_conta.cod_estrutural,3) AS cod_estrutural
                 , receita.cod_receita
                 , conta_receita.exercicio
                 , conta_receita.descricao AS nom_conta
                 , de_para_tipo_retencao.exercicio AS exercicio_tipo
                 , de_para_tipo_retencao.cod_tipo
              FROM empenho.ordem_pagamento_retencao
        INNER JOIN contabilidade.plano_analitica
                ON ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
               AND ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
        INNER JOIN contabilidade.plano_conta
                ON plano_analitica.exercicio = plano_conta.exercicio
               AND plano_analitica.cod_conta = plano_conta.cod_conta
        INNER JOIN orcamento.conta_receita
                ON SUBSTRING(plano_conta.cod_estrutural,3) = conta_receita.cod_estrutural
               AND plano_conta.exercicio = conta_receita.exercicio
        INNER JOIN orcamento.receita
                ON conta_receita.exercicio = receita.exercicio
               AND conta_receita.cod_conta = receita.cod_conta
         LEFT JOIN tcmgo.de_para_tipo_retencao
                ON ordem_pagamento_retencao.exercicio = de_para_tipo_retencao.exercicio
               AND ordem_pagamento_retencao.cod_plano = de_para_tipo_retencao.cod_plano
             WHERE ordem_pagamento_retencao.exercicio = '".$this->getDado('exercicio')."'
               AND plano_conta.cod_estrutural LIKE '4.%'
          GROUP BY ordem_pagamento_retencao.cod_plano
                 , plano_conta.cod_estrutural
                 , receita.cod_receita
                 , conta_receita.exercicio
                 , conta_receita.descricao
                 , de_para_tipo_retencao.exercicio
                 , de_para_tipo_retencao.cod_tipo";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}

?>
