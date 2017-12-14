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
    * Classe de mapeamento da tabela tcesc.tipo_certidao
    * Data de Criação: 27/02/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-03-05 15:47:13 -0300 (Seg, 05 Mar 2007) $

    * Casos de uso: uc-02.08.17
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCESCTipoCertidao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TExportacaoTCESCTipoCertidao()
    {
        parent::Persistente();
        $this->setTabela('tcesc.tipo_certidao');

        $this->setCampoCod('cod_tipo_certidao');
        $this->setComplementoChave('');

        $this->AddCampo('cod_tipo_certidao', 'integer', true,   '', true , false );
        $this->AddCampo('descricao'        , 'char'   , true, '80', false, false );

    }

    public function recuperaTipoCertidao(&$rsRecordSet, $stFiltro="", $stOrder="order by descricao", $boTransacao="")
    {
        return $this->executaRecupera( "montaRecuperaTipoCertidao", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    public function montaRecuperaTipoCertidao()
    {
        $stSQL  = " select cod_tipo_certidao
                         , descricao
                      from tcesc.tipo_certidao ";

        return $stSQL;
    }

}
