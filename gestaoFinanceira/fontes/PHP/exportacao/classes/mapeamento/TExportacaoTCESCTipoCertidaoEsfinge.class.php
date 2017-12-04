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
    * Classe de mapeamento da tabela tcesc.tipo_certidao_esfinge
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

class TExportacaoTCESCTipoCertidaoEsfinge extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TExportacaoTCESCTipoCertidaoEsfinge()
    {
        parent::Persistente();
        $this->setTabela('tcesc.tipo_certidao_esfinge');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_tipo_certidao, cod_documento');

        $this->AddCampo('cod_tipo_certidao', 'integer', true,   '', true , 'TExportacaoTCESCTipoCertidao' );
        $this->AddCampo('cod_documento'    , 'integer', true,   '', true , 'TLicitacaoDocumento' );

    }

    public function recuperaRelacionamento(&$rsRecordSet, $stFiltro="", $stOrder="order by tipo_certidao.descricao", $boTransacao="")
    {
        return $this->executaRecupera( "montaRecuperaRelacionamento", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL  = "
                    select tipo_certidao_esfinge.cod_tipo_certidao
                         , tipo_certidao.descricao
                         , tipo_certidao_esfinge.cod_documento
                         , documento.nom_documento
                      from tcesc.tipo_certidao_esfinge
                      join tcesc.tipo_certidao
                        on tipo_certidao.cod_tipo_certidao = tipo_certidao_esfinge.cod_tipo_certidao
                      join licitacao.documento
                        on documento.cod_documento = tipo_certidao_esfinge.cod_documento
                  ";

        return $stSQL;
    }

}
