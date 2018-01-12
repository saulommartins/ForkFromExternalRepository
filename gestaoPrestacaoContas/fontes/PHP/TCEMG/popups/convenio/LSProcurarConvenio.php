<?php


    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    //include_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoPublicacaoConvenio.class.php'   );
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConvenio.class.php' );
    
    //Define o nome dos arquivos PHP
    $stPrograma = "ProcurarObjeto";
    $pgFilt = "FL".$stPrograma.".php";
    $pgList = "LS".$stPrograma.".php";
    $pgForm = "FM".$stPrograma.".php";
    $pgProc = "PR".$stPrograma.".php";
    $pgOcul = "OC".$stPrograma.".php";

    $stFncJavaScript  = "function insereObjeto(inNumConvenio, stDescricao, stExercicio){
                            window.opener.parent.frames['telaPrincipal'].document.getElementById('".$request->get('campoNom')."').innerHTML = stDescricao;
                            if(window.opener.parent.frames['telaPrincipal'].document.getElementById('stExercicioConvenio')){
                               window.opener.parent.frames['telaPrincipal'].document.getElementById('stExercicioConvenio').value = stExercicio;
                            }
                            window.opener.parent.frames['telaPrincipal'].document.".$request->get('nomForm').".".$request->get('campoNum').".value = inNumConvenio;
                            window.opener.parent.frames['telaPrincipal'].document.".$request->get('nomForm').".".$request->get('campoNum').".focus();
                            window.close();
                         } \n";

    $stCaminho = CAM_GP_COM_INSTANCIAS."objeto/";

    //Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
    $stAcao = $request->get('stAcao', 'excluir');

    switch ($stAcao) {
        case 'alterar': $pgProx = $pgForm; break;
        case 'excluir': $pgProx = $pgProc; break;
        DEFAULT       : $pgProx = $pgForm;
    }

    $stLink = "&stAcao=".$stAcao;

    $filtro = Sessao::read('filtro');
    if ( $request->get('stHdnDescricao') || $request->get('inCodEntidade') || ( $request->get('stDataInicial') && $request->get('stDataFinal') ) || $filtro=='' ){
        foreach ( $request->getAll() as $key => $value ){
            $filtro[$key] = $value;
        }
    }else{
        if( $filtro ){
            foreach ( $filtro as $key => $value ){
                $request->set($key, $value);
            }
        }
        Sessao::write('paginando', true);
    }
    Sessao::write('filtro', $filtro);

    $stFiltro = " WHERE convenio.exercicio = '". $request->get('stExercicioConvenio') ."'";

    $inCodEntidade = $request->get('inCodEntidade');
    if ($inCodEntidade != '') {
        $stFiltro .= " AND convenio.cod_entidade = ".$inCodEntidade."\n";
    }

    $inNumConvenio = $request->get('inNumConvenio');
    if ($inNumConvenio != '') {
        $stFiltro .= " AND convenio.nro_convenio = ".$inNumConvenio."\n";
    }

    $dtInicial = $request->get('dtInicial');
    if ($dtInicial != '') {
        $stFiltro .= " AND convenio.data_inicio >= to_date('".$dtInicial."','dd/mm/yyyy')\n";
    }

    $dtFinal = $request->get('dtFinal');
    if ($dtFinal != '') {
        $stFiltro .= " AND convenio.data_final <= to_date('".$dtFinal."','dd/mm/yyyy')\n";
    }

    $stOrdem = "ORDER BY convenio.nro_convenio ASC";

    $obTTCEMGConvenio = new TTCEMGConvenio;
    $obTTCEMGConvenio->recuperaConvenioFiltro ( $rsLista, $stFiltro, $stOrdem );

    $obLista = new Lista;
    $obLista->obPaginacao->setFiltro("&stLink=".$stLink );

        // $obLista = new Lista;
    $obLista->setRecordSet  ( $rsLista              );
    $obLista->setTitulo     ( "Resultados da Busca" );
    $obLista->setMostraPaginacao ( false            );
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo  ( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth     ( 5 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo  ( "Número do Convênio" );
    $obLista->ultimoCabecalho->setWidth     ( 15 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo  ( "Entidade" );
    $obLista->ultimoCabecalho->setWidth     ( 5 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo  ( "Objeto do Convênio" );
    $obLista->ultimoCabecalho->setWidth     ( 40 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo  ( "Valor" );
    $obLista->ultimoCabecalho->setWidth     ( 15 );
    $obLista->commitCabecalho ();
    
    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo  ( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth     ( 5 );
    $obLista->commitCabecalho ();
    
    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento    ( "DIREITA" );
    $obLista->ultimoDado->setCampo          ( "[nro_convenio]/[exercicio]" );
    $obLista->commitDado ();
    
    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento    ( "ESQUERDA" );
    $obLista->ultimoDado->setCampo          ( "cod_entidade" );
    $obLista->commitDado ();
    
    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento    ( "ESQUERDA" );
    $obLista->ultimoDado->setCampo          ( "objeto" );
    $obLista->commitDado ();
    
    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento    ( "DIREITA" );
    $obLista->ultimoDado->setCampo          ( "vl_convenio" );
    $obLista->commitDado ();

    $stAcao = "SELECIONAR";
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao ( $stAcao );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:insereObjeto();" );
    $obLista->ultimaAcao->addCampo("1","nro_convenio");
    $obLista->ultimaAcao->addCampo("2","objeto");
    $obLista->ultimaAcao->addCampo("3","exercicio");
    $obLista->commitAcao();
    $obLista->show();

    $obFormulario = new Formulario;
    $obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
    $obFormulario->show();
