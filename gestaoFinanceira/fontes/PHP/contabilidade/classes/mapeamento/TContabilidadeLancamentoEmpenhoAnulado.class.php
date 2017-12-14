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
    * Classe de mapeamento da tabela CONTABILIDADE.LANCAMENTO_EMPENHO_ANULADO
    * Data de Criação: 06/07/2016

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TContabilidadeLancamentoEmpenhoAnulado.class.php 66001 2016-07-06 16:51:18Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TContabilidadeLancamentoEmpenhoAnulado extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.lancamento_empenho_anulado');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_lote,tipo,sequencia,cod_entidade,exercicio_anulacao,cod_empenho_anulacao,timestamp_anulacao');

        $this->AddCampo('exercicio'            , 'char'     ,true,'04',true,true);
        $this->AddCampo('cod_lote'             , 'integer'  ,true,  '',true,true);
        $this->AddCampo('tipo'                 , 'char'     ,true, '1',true,true);
        $this->AddCampo('sequencia'            , 'integer'  ,true,  '',true,true);
        $this->AddCampo('cod_entidade'         , 'integer'  ,true,  '',true,true);
        $this->AddCampo('exercicio_anulacao'   , 'char'     ,true,'04',true,true);
        $this->AddCampo('cod_empenho_anulacao' , 'integer'  ,true,  '',true,true);
        $this->AddCampo('timestamp_anulacao'   , 'timestamp',true,  '',true,true);
    }
}
