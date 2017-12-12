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
    * Classe de mapeamento da tabela folhapagamento.bases_evento
    * Data de Criação: 19/08/2008

    * @author Analista: Dagiane
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.05.68

    $Id: TFolhaPagamentoBasesEvento.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFolhaPagamentoBasesEvento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFolhaPagamentoBasesEvento()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.bases_evento");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_base,cod_evento,timestamp');

        $this->AddCampo('cod_base'  ,'integer'      ,true  ,'',true,'TFolhaPagamentoBases');
        $this->AddCampo('cod_evento','integer'      ,true  ,'',true,'TFolhaPagamentoEvento');
        $this->AddCampo('timestamp' ,'timestamp_now',true  ,'',true,false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = " SELECT bases_evento.cod_base                                     \n";
        $stSql .= "       , bases_evento.cod_evento                                  \n";
        $stSql .= "  FROM folhapagamento.bases_evento       \n";
        $stSql .= "     , (SELECT cod_base                                           \n";
        $stSql .= "             , MAX(timestamp) as max_timestamp                    \n";
        $stSql .= "        FROM folhapagamento.bases_evento \n";
        $stSql .= "       GROUP BY cod_base                                          \n";
        $stSql .= "       ) as max_bases_evento                                      \n";
        $stSql .= " WHERE bases_evento.cod_base = max_bases_evento.cod_base          \n";
        $stSql .= "   AND bases_evento.timestamp = max_bases_evento.max_timestamp    \n";

        return $stSql;
    }
}
?>
