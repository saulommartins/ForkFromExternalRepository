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
    * Classe de mapeamento da tabela TCMPA.TIPO_REMUNERACAO
    * Data de Criação: 21/05/2008

    * @author Analista: Gelson W. Golçalves
    * @author Desenvolvedor: Luiz Felipe P Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  TCMPA.LOTACAO
  * Data de Criação: 21/05/2008

  * @author Analista: Gelson W. Golçalves
  * @author Desenvolvedor: Luiz Felipe P Teixeira

*/
class TTPALotacao extends Persistente
{

    /**
      * Método Construtor
      * @access Private
    */
    public function TTPALotacao()
    {
        parent::Persistente();
        $this->setTabela('tcmpa'.Sessao::getEntidade().'.lotacao');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_situacao, cod_sub_divisao','cod_regime','cod_tipo','cod_cargo');

        $this->AddCampo( 'cod_situacao'    , 'integer', true, '', true, false );
        $this->AddCampo( 'cod_sub_divisao', 'integer', true, '', true, true  );
        $this->AddCampo( 'cod_regime', 'integer', true, '', true, true  );
        $this->AddCampo( 'cod_tipo', 'integer', true, '', true, true  );
        $this->AddCampo( 'cod_cargo', 'integer', true, '', true, true  );
    }

    public function recuperaListagemLotacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaListagemLotacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaListagemLotacao()
    {
        $stSQL .= "SELECT situacao_funcional.descricao as descricaoSituacao
                        , tipo_cargo.descricao as descricaoTipoCargo
                        , regime.descricao as descricaoRegime
                        , sub_divisao.descricao as descricaoSubDivisao
                        , lotacao.cod_tipo as inCodTipoCargo
                        , lotacao.cod_regime as inCodRegime
                        , lotacao.cod_situacao as inCodSituacao
                        , lotacao.cod_sub_divisao as inCodSubDivisao
                     FROM tcmpa".Sessao::getEntidade().".lotacao
                    INNER JOIN tcmpa".Sessao::getEntidade().".tipo_cargo USING (cod_tipo)
                    INNER JOIN tcmpa".Sessao::getEntidade().".situacao_funcional USING (cod_situacao)
                    INNER JOIN pessoal".Sessao::getEntidade().".regime USING (cod_regime)
                    INNER JOIN pessoal".Sessao::getEntidade().".sub_divisao USING (cod_sub_divisao)
                    INNER JOIN pessoal".Sessao::getEntidade().".cargo USING (cod_cargo)";

        return $stSQL;
    }

    public function deletaLotacao()
    {
        return $this->executaRecupera("montaDeletaLotacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaDeletaLotacao()
    {
        $stSQL .= "DELETE from tcmpa".Sessao::getEntidade().".lotacao
                    where cod_regime=".$this->getDado('cod_regime')."
                      and cod_sub_divisao=".$this->getDado('cod_sub_divisao');

        return $stSQL;
    }

    public function recuperaCargosLotacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCargosLotacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCargosLotacao()
    {
        $stSQL = "SELECT cod_cargo
                    FROM tcmpa".Sessao::getEntidade().".lotacao";

        return $stSQL;
    }

    public function recuperaLotacionograma(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaLotacionograma",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaLotacionograma()
    {
        $stSQL =  "Select '030' as tipo_registro
                        , lotacao.cod_cargo
                        , cargo.descricao as descricao_cargo
                        , lotacao.cod_tipo as tipo
                        , cargo_sub_divisao.nro_vagas as total_vagas
                        , norma.num_norma as num_norma
                        , to_char(norma.dt_publicacao,'ddmmyyyy') as dt_publicacao
                        , lotacao.cod_sub_divisao
                        , lotacao.cod_situacao
                        , lotacao.cod_regime
                        , '*' as fim_registro
                     from tcmpa".Sessao::getEntidade().".lotacao
                        , pessoal".Sessao::getEntidade().".cargo
                        , pessoal".Sessao::getEntidade().".cargo_sub_divisao
                        , normas".Sessao::getEntidade().".norma
                        , (SELECT cod_sub_divisao
                            , cod_cargo
                            , max(timestamp) as timestamp
                             FROM pessoal.cargo_sub_divisao
                            GROUP BY cod_sub_divisao, cod_cargo
                           ) as max_cargo_sub_divisao
                    where lotacao.cod_cargo 	 		= cargo.cod_cargo
                      AND lotacao.cod_sub_divisao		= cargo_sub_divisao.cod_sub_divisao
                      AND lotacao.cod_cargo 			= cargo_sub_divisao.cod_cargo
                      AND cargo_sub_divisao.cod_sub_divisao     = max_cargo_sub_divisao.cod_sub_divisao
                      AND cargo_sub_divisao.cod_cargo 	  	= max_cargo_sub_divisao.cod_cargo
                      AND cargo_sub_divisao.timestamp 	  	= max_cargo_sub_divisao.timestamp
                      AND cargo_sub_divisao.cod_norma 		= norma.cod_norma";

        return $stSQL;
    }
}
