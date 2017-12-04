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
 * Classe de mapeamento para notificao_termo
 * Data de Criação: 19/11/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Marcio Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TFISNotificacaoTermo.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */
include_once( CAM_GT_FIS_MAPEAMENTO . 'PersistenteAdapter.class.php' );

class TFISNotificacaoTermo extends PersistenteAdapter
{
    /**
     * Método construtor
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTabela( 'fiscalizacao.notificacao_termo' );
        $this->setCampoCod( 'cod_processo' );
        $this->setComplementoChave( 'num_notificacao' );
        // campo, tipo, not_null, data_length, pk, fk
        $this->addCampo( 'cod_processo', 'integer', true, '', true, true );
        $this->addCampo( 'cod_fiscal', 'integer', true, '', false, false );
        $this->addCampo( 'cod_tipo_documento', 'integer', true, '', false, true );
        $this->addCampo( 'cod_documento', 'integer', true, '', false, true );
        $this->addCampo( 'dt_notificacao', 'date', true, '', false, false );
        $this->addCampo( 'observacao', 'text', true, '', false, false );
        $this->addCampo( 'timestamp', 'timestamp', false, '', false, false );
        $this->addCampo( 'num_notificacao', 'serial', true, '', true );
    }

    /**
    * Recupera o último número de notificação
    *
    * @param mixed $rsNumNotificacao
    * @param string $stFiltro
    * @param string $stOrdem
    * @param bool $boTransacao
    *
    * @return RecordSet
    */
    public function recuperaUltimoNumNotificacao(&$rsNumNotificacao, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        $this->executaRecupera("montaRecuperaUltimoNumNotificacao", $rsNumNotificacao, $stFiltro, $stOrder, $boTransacao);
    }

    /**
    * Monta string SQL para recuperar o valor total da receita
    *
    * @return string
    */
    protected function montaRecuperaUltimoNumNotificacao()
    {
        $stSql  = "SELECT MAX(num_notificacao) as num_notificacao FROM fiscalizacao.notificacao_termo     \n";

        return $stSql;
    }

}
?>
