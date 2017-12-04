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
    * Classe Relatório Recibos extra
    * Data de Criação   : 23/08/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.32
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaReciboExtra.class.php" );
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoReciboExtra.class.php";

/**
    * Classe de Regra de Negócios Demonstrativo Saldos
    * @author Desenvolvedor: Tonismar Régis Bernardo
*/

class RTesourariaRelatorioRecibosExtra extends PersistenteRelatorio
{
    /**
        * @var String
        * @access Private
    */
    public $inCodEntidade;

    /**
        * @var Date
        * @access Private
    */
    public $stDataInicial;

    /**
        * @var Date
        * @access Private
    */
    public $stDataFinal;

    /**
        * @var Integer
        * @access Private
    */
    public $inCodCredor;

    /**
        * @var Integer
        * @access Private
    */
    public $inCodRecurso;

    /**
        * @var Integer
        * @access Private
    */
    public $inCodContaBanco;

    /**
        * @var Integer
        * @access Private
    */
    public $inCodContaAnalitica;

    /**
        * @var Integer
        * @access Private
    */
    public $stTipoDemonstracao;

    /**
        * @var String
        * @access Private
    */
    public $stExercicio;
    public $stDestinacaoRecurso;
    public $inCodDetalhamento;
    public $inCodOrdem;
    public $inCodReciboExtra;
    public $stTipoRecibo;

    /** Setters */
    public function setCodEntidade($valor) { $this->inCodEntidade         = $valor; }
    public function setDataInicial($valor) { $this->stDataInicial         = $valor; }
    public function setDataFinal($valor) { $this->stDataFinal           = $valor; }
    public function setCodCredor($valor) { $this->inCodCredor           = $valor; }
    public function setCodRecurso($valor) { $this->inCodRecurso          = $valor; }
    public function setCodContaBanco($valor) { $this->inCodContaBanco       = $valor; }
    public function setFimEstrutural($valor) { $this->stFimEstrutural       = $valor; }
    public function setCodContaAnalitica($valor) { $this->inCodContaAnalitica   = $valor; }
    public function setTipoDemonstracao($valor) { $this->stTipoDemonstracao    = $valor; }
    public function setExercicio($valor) { $this->stExercicio           = $valor; }
    public function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso   = $valor; }
    public function setCodDetalhamento($valor) { $this->inCodDetalhamento     = $valor; }
    public function setCodOrdem($valor) { $this->inCodOrdem            = $valor; }
    public function setCodReciboExtra($valor) { $this->inCodReciboExtra      = $valor; }
    public function setTipoRecibo($valor) { $this->stTipoRecibo          = $valor; }

    /** Getters */
    public function getCodEntidade() { return $this->inCodEntidade;         }
    public function getDataInicial() { return $this->stDataInicial;         }
    public function getDataFinal() { return $this->stDataFinal;           }
    public function getCodCredor() { return $this->inCodCredor;           }
    public function getCodRecurso() { return $this->inCodRecurso;          }
    public function getCodContaBanco() { return $this->inCodContaBanco;       }
    public function getFimEstrutural() { return $this->stFimEstrutural;       }
    public function getCodContaAnalitica() { return $this->inCodContaAnalitica;   }
    public function getTipoDemonstracao() { return $this->stTipoDemonstracao;    }
    public function getExercicio() { return $this->stExercicio;           }
    public function getCodOrdem() { return $this->inCodOrdem;            }
    public function getCodReciboExtra() { return $this->inCodReciboExtra;      }
    public function getTipoRecibo() { return $this->stTipoRecibo;          }

    /**
        * Método Constructor
        * @access Private
    */
    public function RTesourariaRelatorioRecibosExtra()
    {
        $this->obRRelatorio = new RRelatorio;
        $this->obTTesourariaReciboExtra = new TTesourariaReciboExtra();
        $this->obTEmpenhoOrdemPagamentoReciboExtra = new TEmpenhoOrdemPagamentoReciboExtra();
    }

    public function geraRecordSet(&$rsReciboExtra)
    {
        $arReciboExtra = Array();

        $this->obTTesourariaReciboExtra->setDado('inCodEntidade', $this->getCodEntidade());
        $this->obTTesourariaReciboExtra->setDado('stExercicio'  , $this->getExercicio()  );
        $this->obTTesourariaReciboExtra->setDado('stDataInicial', $this->getDataInicial());
        $this->obTTesourariaReciboExtra->setDado('stDataFinal',   $this->getDataFinal()  );
        $this->obTTesourariaReciboExtra->setDado('stTipoDemonstracao', stripslashes($this->getTipoDemonstracao()));

       if( $this->getCodRecurso() )
           $this->obTTesourariaReciboExtra->setDado('inCodRecurso', $this->getCodRecurso());

       if( $this->getCodContaBanco() )
           $this->obTTesourariaReciboExtra->setDado('inCodContaBanco', $this->getCodContaBanco());

       if( $this->getCodContaAnalitica() )
           $this->obTTesourariaReciboExtra->setDado('inCodContaAnalitica', $this->getCodContaAnalitica());

       if( $this->getCodCredor() )
           $this->obTTesourariaReciboExtra->setDado('inCodCredor',$this->getCodCredor());

        $obErro = $this->obTTesourariaReciboExtra->recuperaRelatoriosRecibosExtra( $rsReciboExtra );

        $inCount = 0;
        while (!$rsReciboExtra->eof()) {

        //QUEBRA DE LINHA
            $stNomContaCredor = str_replace( chr(10), "", $rsReciboExtra->getCampo('credor')." - ".$rsReciboExtra->getCampo('nom_cgm'));
            $stNomContaCredor = wordwrap( $stNomContaCredor,50,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
            $arNomContaOLD = explode( chr(13), $stNomContaCredor );         //maiores ou menores

            if ($rsReciboExtra->getCorrente() == 1) {
                $inCount2 = $inCount;
            }

            //FIM DA QUEBRA DE LINHA

            $data = explode("-",$rsReciboExtra->getCampo('data'));
            $data = substr($data[2],0,2)."/".$data[1]."/".$data[0];
            $arReciboExtra[$inCount]['data'       ] = $data;

            //Adicionado foreach para fazer a quebra de linha no campo selecionado
            foreach ($arNomContaOLD as $stNomContaTemp) {
                $arReciboExtra[$inCount2]['credor']    = $stNomContaTemp;
                $inCount2++;
            }
            //fim do foreach

            $arReciboExtra[$inCount]['caixa'      ] = $rsReciboExtra->getCampo('caixa');
            $arReciboExtra[$inCount]['conta'      ] = $rsReciboExtra->getCampo('conta');
            $arReciboExtra[$inCount]['cod_recibo']  = $rsReciboExtra->getCampo('recibo_extra');
            $arReciboExtra[$inCount]['tipo_recibo'] = $rsReciboExtra->getCampo('tipo_recibo');
            $arReciboExtra[$inCount]['recurso'    ] = $rsReciboExtra->getCampo('recurso');
            $arReciboExtra[$inCount]['valor'      ] = $rsReciboExtra->getCampo('valor');
            $arReciboExtra[$inCount]['autenticado'] = $rsReciboExtra->getCampo('autenticado');

            $inCount = $inCount2 - 1;
            $inCount++;
            $rsReciboExtra->proximo();
        }

        $rsReciboExtra  = new RecordSet;
        $rsReciboExtra->preenche($arReciboExtra);

        return $obErro;

    }

    public function geraRecordSetOP(&$arRecordSetRecibos)
    {
        if ($this->getCodOrdem()) {
            $stFiltro .= ' cod_ordem = '.$this->getCodOrdem().' AND ';
        }
        if ($this->getCodEntidade()) {
            $stFiltro .= ' cod_entidade = '.$this->getCodEntidade().' AND ';
        }
        if ($this->getExercicio()) {
            $stFiltro .= " exercicio = '".$this->getExercicio()."' AND ";
        }
        if ($this->getCodReciboExtra()) {
            $stFiltro .= ' cod_recibo_extra = '.$this->getCodReciboExtra().' AND ';
        }
        if ($this->getTipoRecibo()) {
            $stFiltro .= ' tipo_recibo = '.$this->getTipoRecibo().' AND ';
        }

        $stFiltro = ($stFiltro) ? ' WHERE '.substr($stFiltro,0,strlen($stFiltro)-4) : '';

        $this->obTEmpenhoOrdemPagamentoReciboExtra->recuperaTodos($rsOPsReciboExtra, $stFiltro);

        $arRecibos = array();
        while (!$rsOPsReciboExtra->eof()) {
            $inCodReciboExtra = $rsOPsReciboExtra->getCampo('cod_recibo_extra');
            $stExercicio      = $rsOPsReciboExtra->getCampo('exercicio');
            $inCodEntidade    = $rsOPsReciboExtra->getCampo('cod_entidade');
            $stTipoRecibo     = $rsOPsReciboExtra->getCampo('tipo_recibo');

            $stFiltro  = ' WHERE recibo_extra.cod_recibo_extra = '.$inCodReciboExtra;
            $stFiltro .= "   AND recibo_extra.exercicio        = '".$stExercicio."'";
            $stFiltro .= '   AND recibo_extra.cod_entidade     = '.$inCodEntidade;
            $stFiltro .= "   AND recibo_extra.tipo_recibo      = '".$stTipoRecibo."'";

            $this->obTTesourariaReciboExtra->recuperaRelacionamento($rsRecibo, $stFiltro);
            $arReciboTMP['cod_ordem']         = $this->getCodOrdem();
            $arReciboTMP['cod_entidade']      = $rsRecibo->getCampo('cod_entidade');
            $arReciboTMP['cod_recibo_extra']  = $rsRecibo->getCampo('cod_recibo_extra');
            $arReciboTMP['exercicio']         = $rsRecibo->getCampo('exercicio');
            $arReciboTMP['nom_conta']         = $rsRecibo->getCampo('nom_conta');
            $arReciboTMP['cod_estrutural']    = $rsRecibo->getCampo('cod_estrutural');
            $arReciboTMP['cod_plano_despesa'] = $rsRecibo->getCampo('cod_plano_despesa');
            $arReciboTMP['cod_plano_banco']   = $rsRecibo->getCampo('cod_plano_banco');
            $arReciboTMP['valor']             = $rsRecibo->getCampo('valor');
            $arReciboTMP['data']              = $rsRecibo->getCampo('data');
            $arReciboTMP['dt_emissao']        = $rsRecibo->getCampo('dt_emissao');
            $arReciboTMP['cod_credor']        = $rsRecibo->getCampo('cod_credor');
            $arReciboTMP['nom_cgm_credor']    = $rsRecibo->getCampo('nom_cgm_credor');
            $arReciboTMP['cod_recurso']       = $rsRecibo->getCampo('cod_recurso');
            $arReciboTMP['historico']         = $rsRecibo->getCampo('historico');
            $arReciboTMP['nom_recurso']       = $rsRecibo->getCampo('nom_recurso');
            $arReciboTMP['nom_prefeitura']    = $rsRecibo->getCampo('nom_prefeitura');
            $arReciboTMP['nom_municipio']     = $rsRecibo->getCampo('nom_municipio');

            $arRecordSetRecibos[] = $arReciboTMP;
            $rsOPsReciboExtra->proximo();

        }
    }
}
