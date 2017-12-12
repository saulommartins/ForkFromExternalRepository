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
    * Classe de mapeamento da tabela folhapagamento.reajuste
    * Data de Criação: 04/12/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFolhaPagamentoReajuste extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TFolhaPagamentoReajuste()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.reajuste");

        $this->setCampoCod('cod_reajuste');
        $this->setComplementoChave('');

        $this->AddCampo('cod_reajuste' ,'sequence',true  ,''      ,true,false);
        $this->AddCampo('numcgm'       ,'integer' ,true  ,''      ,false,'TAdministracaoUsuario');
        $this->AddCampo('dt_reajuste'  ,'date'    ,true  ,''      ,false,false);
        $this->AddCampo('faixa_inicial','numeric' ,true  ,'14,2'  ,false,false);
        $this->AddCampo('faixa_final'  ,'numeric' ,true  ,'14,2'  ,false,false);
        $this->AddCampo('origem'       ,'char'    ,true  ,'1'     ,false,false);
    }

    public function montaRecuperaReajuste()
    {
        $stSql  = "     SELECT reajuste.cod_reajuste||' - '||                             \n";
        $stSql .= "            to_char(reajuste.dt_reajuste, 'dd/mm/yyyy')||' - '||       \n";
        $stSql .= "            (SELECT 'Valor - '||to_real(valor) as valor                \n";
        $stSql .= "               FROM folhapagamento.reajuste_absoluto                   \n";
        $stSql .= "              WHERE reajuste_absoluto.cod_reajuste = reajuste.cod_reajuste\n";
        $stSql .= "             UNION                                                     \n";
        $stSql .= "             SELECT 'Percentual - '||translate(valor::varchar, '.', ',')||'%' as valor      \n";
        $stSql .= "               FROM folhapagamento.reajuste_percentual                 \n";
        $stSql .= "              WHERE reajuste_percentual.cod_reajuste = reajuste.cod_reajuste\n";
        $stSql .= "            ) as descricao                                             \n";
        $stSql .= "          , reajuste.cod_reajuste                                      \n";
        $stSql .= "          , reajuste.origem                                            \n";
        $stSql .= "       FROM folhapagamento.reajuste                                    \n";

        if (trim($this->getDado("cod_evento")) != "") {
            switch ($this->getDado("cod_configuracao")) {
                case 0:
                    $stSql .= "WHERE reajuste.cod_reajuste IN (SELECT cod_reajuste \n";
                    $stSql .= "                                  FROM folhapagamento.reajuste_registro_evento_complementar\n";
                    $stSql .= "                                 WHERE reajuste_registro_evento_complementar.cod_evento = ".$this->getDado("cod_evento").")\n";
                    break;
                case 1:
                    $stSql .= "WHERE reajuste.cod_reajuste IN (SELECT cod_reajuste \n";
                    $stSql .= "                                  FROM folhapagamento.reajuste_registro_evento\n";
                    $stSql .= "                                 WHERE reajuste_registro_evento.cod_evento = ".$this->getDado("cod_evento").")\n";
                    break;
                case 2:
                    $stSql .= "WHERE reajuste.cod_reajuste IN (SELECT cod_reajuste \n";
                    $stSql .= "                                  FROM folhapagamento.reajuste_registro_evento_ferias\n";
                    $stSql .= "                                 WHERE reajuste_registro_evento_ferias.cod_evento = ".$this->getDado("cod_evento").")\n";
                    break;
                case 3:
                    $stSql .= "WHERE reajuste.cod_reajuste IN (SELECT cod_reajuste \n";
                    $stSql .= "                                  FROM folhapagamento.reajuste_registro_evento_decimo\n";
                    $stSql .= "                                 WHERE reajuste_registro_evento_decimo.cod_evento = ".$this->getDado("cod_evento").")\n";
                    break;
                case 4:
                    $stSql .= "WHERE reajuste.cod_reajuste IN (SELECT cod_reajuste \n";
                    $stSql .= "                                  FROM folhapagamento.reajuste_registro_evento_rescisao\n";
                    $stSql .= "                                 WHERE reajuste_registro_evento_rescisao.cod_evento = ".$this->getDado("cod_evento").")\n";
                    break;
            }
        }

        return $stSql;
    }

    public function recuperaReajuste(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaReajuste",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "SELECT cod_reajuste
                        , numcgm
                        , dt_reajuste
                        , faixa_inicial
                        , faixa_final
                        , origem
                        , (SELECT valor FROM folhapagamento.reajuste_percentual WHERE reajuste_percentual.cod_reajuste = reajuste.cod_reajuste) as percentual
                        , (SELECT valor FROM folhapagamento.reajuste_absoluto WHERE reajuste_absoluto.cod_reajuste = reajuste.cod_reajuste) as valor
                     FROM folhapagamento.reajuste";

        return $stSql;
    }
}
?>
