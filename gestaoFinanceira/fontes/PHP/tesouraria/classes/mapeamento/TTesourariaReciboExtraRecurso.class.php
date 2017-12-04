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
    * Classe de mapeamento da tabela RECIBO_EXTRA_RECURSO
    * Data de Criação: 04/08/2006

    * @author Analista: Cleisson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.04.30, 02.04.29
*/

/*
$Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaReciboExtraRecurso extends Persistente
{
   public function TTesourariaReciboExtraRecurso()
   {
        parent::Persistente();

        $this->setTabela( 'tesouraria.recibo_extra_recurso' );
        $this->setCampoCod ( '' );
        $this->setComplementoChave ( 'cod_entidade, exercicio, cod_recibo_extra, tipo_recibo' );

        $this->addCampo('cod_entidade'     , 'integer'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('exercicio'        , 'varchar'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('cod_recibo_extra' , 'integer'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('tipo_recibo'      , 'varchar'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('cod_recurso'      , 'integer'  ,true  , ''  , false , false );

   }
}
